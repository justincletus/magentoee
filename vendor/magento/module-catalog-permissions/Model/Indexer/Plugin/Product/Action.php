<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin\Product;

use Magento\CatalogPermissions\Model\Indexer\Plugin\AbstractProduct;

class Action extends AbstractProduct
{
    /**
     * Reindex product permissions on product attribute mass change
     *
     * @param \Magento\Catalog\Model\Product\Action $subject
     * @param \Closure $closure
     * @param int[] $productIds
     * @param int[] $attrData
     * @param int $storeId
     * @return \Magento\Catalog\Model\Product\Action
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundUpdateAttributes(
        \Magento\Catalog\Model\Product\Action $subject,
        \Closure $closure,
        $productIds,
        $attrData,
        $storeId
    ) {
        $action = $closure($productIds, $attrData, $storeId);
        $this->reindex($productIds);
        return $action;
    }

    /**
     * Reindex product permissions on product websites mass change
     *
     * @param \Magento\Catalog\Model\Product\Action $subject
     * @param \Closure $closure
     * @param int[] $productIds
     * @param int[] $websiteIds
     * @param string $type
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundUpdateWebsites(
        \Magento\Catalog\Model\Product\Action $subject,
        \Closure $closure,
        $productIds,
        $websiteIds,
        $type
    ) {
        $closure($productIds, $websiteIds, $type);
        $this->reindex($productIds);
    }
}
