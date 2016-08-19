<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer;

/**
 * Customer attributes grid container
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Attribute extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Define controller, block and labels
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_CustomerCustomAttributes';
        $this->_controller = 'adminhtml_customer_attribute';
        $this->_headerText = __('Customer Attributes');
        $this->_addButtonLabel = __('Add New Attribute');
        parent::_construct();
    }
}
