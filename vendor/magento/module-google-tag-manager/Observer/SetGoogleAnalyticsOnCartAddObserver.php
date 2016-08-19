<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GoogleTagManager\Observer;

use Magento\Framework\Event\ObserverInterface;

class SetGoogleAnalyticsOnCartAddObserver implements ObserverInterface
{
    /**
     * @var \Magento\GoogleTagManager\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\GoogleTagManager\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\GoogleTagManager\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $registry
    ) {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->registry = $registry;
    }

    /**
     * Fired by sales_quote_product_add_after event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isTagManagerAvailable()) {
            return $this;
        }
        $products = $this->registry->registry('GoogleTagManager_products_addtocart');
        if (!$products) {
            $products = [];
        }
        $lastValues = [];
        if ($this->checkoutSession->hasData(
            \Magento\GoogleTagManager\Helper\Data::PRODUCT_QUANTITIES_BEFORE_ADDTOCART
        )) {
            $lastValues = $this->checkoutSession->getData(
                \Magento\GoogleTagManager\Helper\Data::PRODUCT_QUANTITIES_BEFORE_ADDTOCART
            );
        }

        $items = $observer->getEvent()->getItems();
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        foreach ($items as $quoteItem) {
            $id = $quoteItem->getProductId();
            $parentQty = 1;
            $price = $quoteItem->getProduct()->getPrice();
            switch ($quoteItem->getProductType()) {
                case 'configurable':
                case 'bundle':
                    break ;
                case 'grouped':
                    $id = $quoteItem->getOptionByCode('product_type')->getProductId() . '-'
                        . $quoteItem->getProductId();
                    // no break;
                default:
                    if ($quoteItem->getParentItem()) {
                        $parentQty = $quoteItem->getParentItem()->getQty();
                        $id = $quoteItem->getId() . '-' .
                            $quoteItem->getParentItem()->getProductId() . '-' .
                            $quoteItem->getProductId();

                        if ($quoteItem->getParentItem()->getProductType() == 'configurable') {
                            $price = $quoteItem->getParentItem()->getProduct()->getPrice();
                        }
                    }
                    if ($quoteItem->getProductType() == 'giftcard') {
                        $price = $quoteItem->getProduct()->getFinalPrice();
                    }

                    $oldQty = (array_key_exists($id, $lastValues)) ? $lastValues[$id] : 0;
                    $finalQty = ($parentQty * $quoteItem->getQty()) - $oldQty;
                    if ($finalQty != 0) {
                        $products[] = [
                            'sku'   => $quoteItem->getSku(),
                            'name'  => $quoteItem->getName(),
                            'price' => $price,
                            'qty'   => $finalQty
                        ];
                    }
            }
        }
        $this->registry->unregister('GoogleTagManager_products_addtocart');
        $this->registry->register('GoogleTagManager_products_addtocart', $products);
        $this->checkoutSession->unsetData(\Magento\GoogleTagManager\Helper\Data::PRODUCT_QUANTITIES_BEFORE_ADDTOCART);

        return $this;
    }
}
