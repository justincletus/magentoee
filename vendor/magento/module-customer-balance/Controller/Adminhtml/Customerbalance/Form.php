<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerBalance\Controller\Adminhtml\Customerbalance;

class Form extends \Magento\CustomerBalance\Controller\Adminhtml\Customerbalance
{
    /**
     * Customer balance form
     *
     * @return void
     */
    public function execute()
    {
        $this->initCurrentCustomer();
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
