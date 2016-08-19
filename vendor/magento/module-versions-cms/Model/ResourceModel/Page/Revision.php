<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Model\ResourceModel\Page;

/**
 * Cms page revision resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Revision extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Name of page table from config
     *
     * @var string
     */
    protected $_pageTable;

    /**
     * Name of version table from config
     *
     * @var string
     */
    protected $_versionTable;

    /**
     * Alias of page table from config
     *
     * @var string
     */
    protected $_pageTableAlias;

    /**
     * Alias of version table from config
     *
     * @var string
     */
    protected $_versionTableAlias;

    /**
     * Resource initialization. Define the table names and its aliases.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_versionscms_page_revision', 'revision_id');

        $this->_pageTable = $this->getTable('cms_page');
        $this->_versionTable = $this->getTable('magento_versionscms_page_version');
        $this->_pageTableAlias = 'page_table';
        $this->_versionTableAlias = 'version_table';
    }

    /**
     * Process page data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getCopiedFromOriginal()) {
            /*
             * For two attributes which represent datetime data in DB
             * we should make converting such as:
             * If they are empty we need to convert them into DB
             * type NULL so in DB they will be empty and not some default value.
             */
            foreach (['custom_theme_from', 'custom_theme_to'] as $dataKey) {
                $date = $object->getData($dataKey);
                if (!$date) {
                    $object->setData($dataKey, new \Zend_Db_Expr('NULL'));
                }
            }
        }
        return parent::_beforeSave($object);
    }

    /**
     * Process data after save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->_aggregateVersionData((int)$object->getVersionId());

        return parent::_afterSave($object);
    }

    /**
     * Process data after delete
     * Validate if this revision can be removed
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->_aggregateVersionData((int)$object->getVersionId());

        return parent::_afterDelete($object);
    }

    /**
     * Checking if revision was published
     *
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    public function isRevisionPublished(\Magento\Framework\Model\AbstractModel $object)
    {
        $select = $this->getConnection()->select();
        $select->from($this->_pageTable, 'published_revision_id')->where('page_id = ?', (int)$object->getPageId());

        $result = $this->getConnection()->fetchOne($select);

        return $result == $object->getId();
    }

    /**
     * Aggregate data for version
     *
     * @param int $versionId
     * @return $this
     */
    protected function _aggregateVersionData($versionId)
    {
        $connection = $this->getConnection();
        $selectCount = $connection->select()->from(
            $this->getMainTable(),
            ['version_id', 'revisions_count' => 'COUNT(1)']
        )->where(
            'version_id = ?',
            (int)$versionId
        )->group(
            'version_id'
        );

        $sql = new \Zend_Db_Expr(sprintf('(%s)', $selectCount));
        $select = clone $selectCount;
        $select->reset()->join(
            ['r' => $sql],
            'p.version_id = r.version_id',
            ['revisions_count']
        )->where(
            'r.version_id = ?',
            (int)$versionId
        );

        $connection = $this->getConnection();
        $query = $connection->updateFromSelect($select, ['p' => $this->_versionTable]);
        $connection->query($query);

        return $this;
    }

    /**
     * Publishing passed revision object to page
     *
     * @param \Magento\VersionsCms\Model\Page\Revision $object
     * @param int $targetId
     * @return $this
     */
    public function publish(\Magento\VersionsCms\Model\Page\Revision $object, $targetId)
    {
        $data = $this->_prepareDataForTable($object, $this->_pageTable);
        $condition = ['page_id = ?' => $targetId];
        $this->getConnection()->update($this->_pageTable, $data, $condition);

        return $this;
    }

    /**
     * Loading revision's data with extra access level checking.
     *
     * @param \Magento\VersionsCms\Model\Page\Revision $object
     * @param string|string[] $accessLevel
     * @param int $userId
     * @param int|string $value
     * @param string|null $field
     * @return $this
     */
    public function loadWithRestrictions($object, $accessLevel, $userId, $value, $field)
    {
        if ($field === null) {
            $field = $this->getIdFieldName();
        }

        $connection = $this->getConnection();
        if ($value) {
            // getting main load select
            $select = $this->_getLoadSelect($field, $value, $object);

            // prepare join conditions for version table
            $joinConditions = [$this->_getPermissionCondition($accessLevel, $userId)];
            $joinConditions[] = sprintf(
                '%s.version_id = %s.version_id',
                $this->_versionTableAlias,
                $this->getMainTable()
            );
            // joining version table
            $this->_joinVersionData($select, 'joinInner', implode(' AND ', $joinConditions));

            // prepare join conditions for page table
            $joinConditions = sprintf('%s.page_id = %s.page_id', $this->getMainTable(), $this->_pageTableAlias);
            // joining page table
            $this->_joinPageData($select, 'joinInner', $joinConditions);

            if ($field != $this->getIdFieldName()) {
                // Adding limitation and ordering bc we are
                // loading not by unique conditions so we need
                // to make sure we have latest revision and only one
                $this->_addSingleLimitation($select);
            }

            $data = $connection->fetchRow($select);
            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);
        return $this;
    }

    /**
     * Loading revision's data using version and page's id but also counting on access restrictions.
     * Used to load clean revision without any data that is under revision control but which
     * will have all other data from version and page tables.
     *
     * @param \Magento\VersionsCms\Model\Page\Revision $object
     * @param int $versionId
     * @param int $pageId
     * @param string|string[] $accessLevel
     * @param int $userId
     * @return $this
     */
    public function loadByVersionPageWithRestrictions($object, $versionId, $pageId, $accessLevel, $userId)
    {
        $connection = $this->getConnection();
        if ($versionId && $pageId) {
            // getting main load select
            $select = $this->_getLoadSelect($this->getIdFieldName(), false, $object);
            // reseting all columns and where as we don't have need them
            $select->reset(\Magento\Framework\DB\Select::COLUMNS)->reset(\Magento\Framework\DB\Select::WHERE);

            // adding where conditions with restriction filter
            $whereConditions = [$this->_getPermissionCondition($accessLevel, $userId)];
            $whereConditions[] = $connection->quoteInto($this->_versionTableAlias . '.version_id = ?', $versionId);
            $select->where(implode(' AND ', $whereConditions));

            //joining version table
            $this->_joinVersionData($select, 'joinRight', '1 = 1');

            //joining page table
            $joinCondition = $connection->quoteInto($this->_pageTableAlias . '.page_id = ?', $pageId);
            $this->_joinPageData($select, 'joinLeft', $joinCondition);
            // adding page id column which we will not have as this is clean revision
            // and this column is not specified in join
            $select->columns('page_table.page_id');

            // Adding limitation and ordering bc we are
            // loading not by unique conditions so we need
            // to make sure we have latest revision and only one
            $this->_addSingleLimitation($select);

            $data = $connection->fetchRow($select);
            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);
        return $this;
    }

    /**
     * Preparing array of conditions based on user id and version's access level.
     *
     * @param string|string[] $accessLevel
     * @param int $userId
     * @return string
     */
    protected function _getPermissionCondition($accessLevel, $userId)
    {
        $connection = $this->getConnection();
        $permissionCondition = [];
        $permissionCondition[] = $connection->quoteInto($this->_versionTableAlias . '.user_id = ? ', $userId);

        if (is_array($accessLevel) && !empty($accessLevel)) {
            $permissionCondition[] = $connection->quoteInto(
                $this->_versionTableAlias . '.access_level IN (?)',
                $accessLevel
            );
        } elseif ($accessLevel) {
            $permissionCondition[] = $connection->quoteInto(
                $this->_versionTableAlias . '.access_level = ?',
                $accessLevel
            );
        } else {
            $permissionCondition[] = $this->_versionTableAlias . '.access_level = ""';
        }

        return sprintf('(%s)', implode(' OR ', $permissionCondition));
    }

    /**
     * Joining version table using specified conditions and join type.
     *
     * @param \Magento\Framework\DB\Select $select
     * @param string $joinType
     * @param string $joinConditions
     * @return \Magento\Framework\DB\Select
     */
    protected function _joinVersionData($select, $joinType, $joinConditions)
    {
        $select->{$joinType}(
            [$this->_versionTableAlias => $this->_versionTable],
            $joinConditions,
            ['version_id', 'version_number', 'label', 'access_level', 'version_user_id' => 'user_id']
        );

        return $select;
    }

    /**
     * Joining page table using specified conditions and join type.
     *
     * @param \Magento\Framework\DB\Select $select
     * @param string $joinType can be joinInner, joinRight, joinLeft
     * @param string $joinConditions
     * @return \Magento\Framework\DB\Select
     */
    protected function _joinPageData($select, $joinType, $joinConditions)
    {
        $select->{$joinType}([$this->_pageTableAlias => $this->_pageTable], $joinConditions, ['title']);

        return $select;
    }

    /**
     * Applying order by create datetime and limitation to one record.
     *
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    protected function _addSingleLimitation($select)
    {
        $select->order($this->getMainTable() . '.created_at ' . \Magento\Framework\DB\Select::SQL_DESC)->limit(1);
        return $select;
    }
}
