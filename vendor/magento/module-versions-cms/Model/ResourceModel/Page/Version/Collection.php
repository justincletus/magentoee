<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Model\ResourceModel\Page\Version;

/**
 * Cms page version collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\VersionsCms\Model\ResourceModel\Page\Collection\AbstractCollection
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\VersionsCms\Model\Page\Version', 'Magento\VersionsCms\Model\ResourceModel\Page\Version');
    }

    /**
     * Add access level filter.
     * Can take parameter array or one level.
     *
     * @param mixed $level
     * @return $this
     */
    public function addAccessLevelFilter($level)
    {
        if (is_array($level)) {
            $condition = ['in' => $level];
        } else {
            $condition = $level;
        }

        $this->addFieldToFilter('access_level', $condition);
        return $this;
    }

    /**
     * Prepare two dimensional array basing on version_id as key and
     * version label as value data from collection.
     *
     * @return array
     */
    public function getIdLabelArray()
    {
        return $this->_toOptionHash('version_id', 'version_label');
    }

    /**
     * Prepare two dimensional array basing on key and value field.
     *
     * @param string $keyField
     * @param string $valueField
     * @return array
     */
    public function getAsArray($keyField, $valueField)
    {
        $data = $this->_toOptionHash($keyField, $valueField);
        return array_filter($data);
    }

    /**
     * Join revision data by version id
     *
     * @return $this
     */
    public function joinRevisions()
    {
        if (!$this->getFlag('revisions_joined')) {
            $this->getSelect()->joinLeft(
                ['rev_table' => $this->getTable('magento_versionscms_page_revision')],
                'rev_table.version_id = main_table.version_id',
                '*'
            );

            $this->setFlag('revisions_joined');
        }
        return $this;
    }

    /**
     * Add order by version number in specified direction.
     *
     * @param string $dir
     * @return $this
     */
    public function addNumberSort($dir = \Magento\Framework\DB\Select::SQL_DESC)
    {
        $this->setOrder('version_number', $dir);
        return $this;
    }
}
