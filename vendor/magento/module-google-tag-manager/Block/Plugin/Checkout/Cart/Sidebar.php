<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GoogleTagManager\Block\Plugin\Checkout\Cart;

class Sidebar
{
    /**
     * @var \Magento\GoogleTagManager\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\GoogleTagManager\Helper\Data $helper
     */
    public function __construct(\Magento\GoogleTagManager\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Sidebar $subject
     * @param string $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterToHtml(\Magento\Checkout\Block\Cart\Sidebar $subject, $result)
    {
        if (!$this->helper->isTagManagerAvailable()) {
            return $result;
        }

        /** @var \Magento\GoogleTagManager\Block\ListJson $jsonBlock */
        $jsonBlock = $subject->getLayout()->getBlock('update_cart_analytics');
        return $result . $jsonBlock->toHtml();
    }
}
