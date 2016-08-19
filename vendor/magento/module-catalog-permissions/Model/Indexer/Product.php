<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Model\Indexer;

class Product extends Category
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'catalogpermissions_product';

    /**
     * @param Category\Action\FullFactory $fullActionFactory
     * @param Product\Action\RowsFactory $rowsActionFactory
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     */
    public function __construct(
        Category\Action\FullFactory $fullActionFactory,
        Product\Action\RowsFactory $rowsActionFactory,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    ) {
        parent::__construct($fullActionFactory, $rowsActionFactory, $indexerRegistry);
    }
}
