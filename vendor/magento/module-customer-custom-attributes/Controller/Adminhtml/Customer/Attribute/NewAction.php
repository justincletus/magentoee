<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Attribute;

class NewAction extends \Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Attribute
{
    /**
     * Create new attribute action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->addActionLayoutHandles();
        $this->_forward('edit');
    }
}
