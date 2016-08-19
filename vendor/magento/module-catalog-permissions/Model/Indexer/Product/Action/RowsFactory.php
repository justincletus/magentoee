<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Model\Indexer\Product\Action;

/**
 * Factory class for \Magento\CatalogPermissions\Model\Indexer\Product\Action\Rows
 */
class RowsFactory extends \Magento\CatalogPermissions\Model\Indexer\Category\Action\RowsFactory
{
    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = 'Magento\CatalogPermissions\Model\Indexer\Product\Action\Rows'
    ) {
        parent::__construct($objectManager, $instanceName);
    }
}
