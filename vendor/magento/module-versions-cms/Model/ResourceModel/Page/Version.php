<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Model\ResourceModel\Page;

/**
 * Cms page version resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Version extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_versionscms_page_version', 'version_id');
    }

    /**
     * Checking if version id not last public for its page
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    public function isVersionLastPublic(\Magento\Framework\Model\AbstractModel $object)
    {
        $select = $this->getConnection()->select();
        $select->from(
            $this->getMainTable(),
            'COUNT(*)'
        )->where(
            implode(
                ' AND ',
                ['page_id      = :page_id', 'access_level = :access_level', 'version_id   = :version_id']
            )
        );

        $bind = [
            ':page_id' => $object->getPageId(),
            ':access_level' => \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PUBLIC,
            ':version_id' => $object->getVersionId(),
        ];

        return !$this->getConnection()->fetchOne($select, $bind);
    }

    /**
     * Checking if Version does not contain published revision
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    public function isVersionHasPublishedRevision(\Magento\Framework\Model\AbstractModel $object)
    {
        $select = $this->getConnection()->select();
        $select->from(
            ['p' => $this->getTable('cms_page')],
            []
        )->where(
            'p.page_id = ?',
            (int)$object->getPageId()
        )->join(
            ['r' => $this->getTable('magento_versionscms_page_revision')],
            'r.revision_id = p.published_revision_id',
            'r.version_id'
        );

        $result = $this->getConnection()->fetchOne($select);

        return $result == $object->getVersionId();
    }

    /**
     * Add access restriction filters to allow load only by granted user.
     *
     * @param \Magento\Framework\DB\Select $select
     * @param int $accessLevel
     * @param int $userId
     * @return \Magento\Framework\DB\Select
     */
    protected function _addAccessRestrictionsToSelect($select, $accessLevel, $userId)
    {
        $conditions = [];

        $conditions[] = $this->getConnection()->quoteInto('user_id = ?', (int)$userId);

        if (!empty($accessLevel)) {
            if (!is_array($accessLevel)) {
                $accessLevel = [$accessLevel];
            }
            $conditions[] = $this->getConnection()->quoteInto('access_level IN (?)', $accessLevel);
        } else {
            $conditions[] = 'access_level IS NULL';
        }

        $select->where(implode(' OR ', $conditions));

        return $select;
    }

    /**
     * Loading data with extra access level checking.
     *
     * @param \Magento\VersionsCms\Model\Page\Version $object
     * @param array|string $accessLevel
     * @param int $userId
     * @param int|string $value
     * @param string|null $field
     * @return $this
     */
    public function loadWithRestrictions($object, $accessLevel, $userId, $value, $field = null)
    {
        if ($field === null) {
            $field = $this->getIdFieldName();
        }

        $connection = $this->getConnection();
        if ($value) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select = $this->_addAccessRestrictionsToSelect($select, $accessLevel, $userId);
            $data = $connection->fetchRow($select);
            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);
        return $this;
    }
}
