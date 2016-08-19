<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GoogleTagManager\Block;

class Ga extends \Magento\GoogleAnalytics\Block\Ga
{
    /**
     * @var \Magento\GoogleAnalytics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Cookie\Helper\Cookie
     */
    protected $cookieHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection
     * @param \Magento\GoogleTagManager\Helper\Data $googleAnalyticsData
     * @param \Magento\Cookie\Helper\Cookie $cookieHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection,
        \Magento\GoogleTagManager\Helper\Data $googleAnalyticsData,
        \Magento\Cookie\Helper\Cookie $cookieHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data = []
    ) {
        $this->cookieHelper = $cookieHelper;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $salesOrderCollection, $googleAnalyticsData, $data);
    }
    /**
     * Is gtm available
     *
     * @return bool
     */
    protected function _isAvailable()
    {
        return $this->_googleAnalyticsData->isGoogleAnalyticsAvailable();
    }

    /**
     * Render GA tracking scripts
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_isAvailable()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Get store currency code for page tracking javascript code
     *
     * @return string
     */
    public function getStoreCurrencyCode()
    {
        return $this->_storeManager->getStore()->getBaseCurrencyCode();
    }


    /**
     * Render information about specified orders and their items
     * @return string
     */
    public function getOrdersData()
    {
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return '';
        }
        $collection = $this->_salesOrderCollection->create();
        $collection->addFieldToFilter('entity_id', ['in' => $orderIds]);

        $result = [];
        /** @var \Magento\Sales\Model\Order $order */
        foreach ($collection as $order) {
            $actionField['id'] = $order->getIncrementId();
            $actionField['revenue'] = $order->getBaseGrandTotal() -
                ($order->getBaseTaxAmount() + $order->getBaseShippingAmount());
            $actionField['tax'] = $order->getBaseTaxAmount();
            $actionField['shipping'] = $order->getBaseShippingAmount();
            $actionField['coupon'] = (string)$order->getCouponCode();

            $products = [];
            /** @var \Magento\Sales\Model\Order\Item $item*/
            foreach ($order->getAllVisibleItems() as $item) {
                $product['id'] = $item->getSku();
                $product['name'] = $item->getName();
                $product['price'] = $item->getBasePrice();
                $product['quantity'] = $item->getQtyOrdered();
                //$product['category'] = ''; //Not available to populate
                $products[] = $product;
            }
            $json['ecommerce']['purchase']['actionField'] = $actionField;
            $json['ecommerce']['purchase']['products'] = $products;
            $json['ecommerce']['currencyCode'] = $this->getStoreCurrencyCode();
            $json['event'] = 'purchase';
            $result[] = 'dataLayer.push(' . $this->jsonHelper->jsonEncode($json) . ");\n";
        }
        return implode("\n", $result);
    }

    /**
     * @return bool
     */
    public function isUserNotAllowSaveCookie()
    {
        return $this->cookieHelper->isUserNotAllowSaveCookie();
    }
}
