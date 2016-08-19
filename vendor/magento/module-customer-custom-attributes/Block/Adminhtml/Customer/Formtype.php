<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer;

/**
 * Form Types Grid Container Block
 *
 */
class Formtype extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Block constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_CustomerCustomAttributes';
        $this->_controller = 'adminhtml_customer_formtype';
        $this->_headerText = __('Manage Form Types');

        parent::_construct();

        $this->buttonList->update('add', 'label', __('New Form Type'));
    }
}
