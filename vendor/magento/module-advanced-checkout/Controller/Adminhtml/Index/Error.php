<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml\Index;

class Error extends \Magento\AdvancedCheckout\Controller\Adminhtml\Index
{
    /**
     * Empty page for final errors occurred
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_initTitle();
        $this->_view->renderLayout();
    }
}
