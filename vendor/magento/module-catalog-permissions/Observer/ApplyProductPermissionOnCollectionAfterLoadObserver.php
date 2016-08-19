<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Observer;

use Magento\CatalogPermissions\App\ConfigInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class ApplyProductPermissionOnCollectionAfterLoadObserver implements ObserverInterface
{
    /**
     * Permissions configuration instance
     *
     * @var ConfigInterface
     */
    protected $_permissionsConfig;

    /**
     * @var ApplyPermissionsOnProduct
     */
    protected $applyPermissionsOnProduct;

    /**
     * Constructor
     *
     * @param ConfigInterface $permissionsConfig
     * @param ApplyPermissionsOnProduct $applyPermissionsOnProduct
     */
    public function __construct(
        ConfigInterface $permissionsConfig,
        ApplyPermissionsOnProduct $applyPermissionsOnProduct
    ) {
        $this->_permissionsConfig = $permissionsConfig;
        $this->applyPermissionsOnProduct = $applyPermissionsOnProduct;
    }

    /**
     * Apply category permissions for collection on after load
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->_permissionsConfig->isEnabled()) {
            return $this;
        }

        $collection = $observer->getEvent()->getCollection();
        foreach ($collection as $product) {
            if ($collection->hasFlag('product_children')) {
                $product->addData(
                    [
                        'grant_catalog_category_view' => -1,
                        'grant_catalog_product_price' => -1,
                        'grant_checkout_items' => -1
                    ]
                );
            }
            $this->applyPermissionsOnProduct->execute($product);
        }
        return $this;
    }
}
