<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesArchive\Model\ResourceModel\Plugin;

/**
 * Plugin 'sales-archive-move-to-active' for order grid refresh
 */
class Grid
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\GridPool
     */
    protected $gridPool;
    /**
     * @var \Magento\SalesArchive\Model\ResourceModel\Archive
     */
    protected $archive;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @param \Magento\Sales\Model\ResourceModel\GridPool $gridPool
     * @param \Magento\SalesArchive\Model\ResourceModel\Archive $archive
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\GridPool $gridPool,
        \Magento\SalesArchive\Model\ResourceModel\Archive $archive,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->gridPool = $gridPool;
        $this->archive = $archive;
        $this->resource = $resource;
    }

    /**
     * Removes order from archive and refreshes grids
     *
     * @param \Magento\Sales\Model\ResourceModel\Grid $grid
     * @param \Closure $proceed
     * @param string $value
     * @param string|null $field
     * @return \Magento\Sales\Model\ResourceModel\GridPool | \Zend_Db_Statement_Interface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundRefresh(
        \Magento\Sales\Model\ResourceModel\Grid $grid,
        \Closure $proceed,
        $value,
        $field = null
    ) {
        if ($grid->getGridTable() == $this->resource->getTableName('sales_order')) {
            if ($this->archive->isOrderInArchive($value)) {
                $this->archive->removeOrdersFromArchiveById([$value]);
                $this->gridPool->refreshByOrderId($value);
            }
        }

        return $proceed($value, $field);
    }
}
