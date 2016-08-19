<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCheckout\Test\Unit\Controller\Adminhtml\Stub;

class Child extends \Magento\AdvancedCheckout\Controller\Adminhtml\Index
{
    public function execute()
    {
        $this->_initData();
    }
}
