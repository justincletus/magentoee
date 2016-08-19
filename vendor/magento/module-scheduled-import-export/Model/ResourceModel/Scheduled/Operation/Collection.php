<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ScheduledImportExport\Model\ResourceModel\Scheduled\Operation;

/**
 * Operation resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\ScheduledImportExport\Model\Scheduled\Operation',
            'Magento\ScheduledImportExport\Model\ResourceModel\Scheduled\Operation'
        );
    }

    /**
     * Call afterLoad method for each item
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $item->afterLoad();
        }

        return parent::_afterLoad();
    }
}
