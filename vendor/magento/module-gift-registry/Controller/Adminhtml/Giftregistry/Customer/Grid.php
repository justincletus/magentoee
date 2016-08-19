<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Controller\Adminhtml\Giftregistry\Customer;

/**
 * @codeCoverageIgnore
 */
class Grid extends \Magento\GiftRegistry\Controller\Adminhtml\Giftregistry\Customer
{
    /**
     * Get customer gift registry grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
