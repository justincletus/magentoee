<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCheckout\Controller\Sku;

class Index extends \Magento\AdvancedCheckout\Controller\Sku
{
    /**
     * View Order by SKU page in 'My Account' section
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Order by SKU'));
        $this->_view->renderLayout();
    }
}
