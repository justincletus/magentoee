<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GoogleTagManager\Observer;

use Magento\Framework\Event\ObserverInterface;

class SetGoogleAnalyticsOnCartRemoveObserver implements ObserverInterface
{
    /**
     * @var \Magento\GoogleTagManager\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\GoogleTagManager\Helper\Data $helper
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\GoogleTagManager\Helper\Data $helper,
        \Magento\Framework\Registry $registry
    ) {
        $this->helper = $helper;
        $this->registry = $registry;
    }

    /**
     * Fired by sales_quote_remove_item event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isTagManagerAvailable()) {
            return $this;
        }
        $products = $this->registry->registry('GoogleTagManager_products_to_remove');
        if (!$products) {
            $products = [];
        }
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        $quoteItem = $observer->getEvent()->getQuoteItem();
        if ($simples = $quoteItem->getChildren() and $quoteItem->getProductType() != 'configurable') {
            foreach ($simples as $item) {
                $products[] = [
                    'sku'   => $item->getSku(),
                    'name'  => $item->getName(),
                    'price' => $item->getPrice(),
                    'qty' => $item->getQty()
                ];
            }
        } else {
            $products[] = [
                'sku' => $quoteItem->getSku(),
                'name' => $quoteItem->getName(),
                'price' => $quoteItem->getProduct()->getPrice(),
                'qty' => $quoteItem->getQty()
            ];
        }
        $this->registry->unregister('GoogleTagManager_products_to_remove');
        $this->registry->register('GoogleTagManager_products_to_remove', $products);

        return $this;
    }
}
