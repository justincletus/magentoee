<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSegment\Block\Adminhtml\Report\Customer;

/**
 * Customer Segments grid container
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Segment extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_CustomerSegment';
        $this->_controller = 'adminhtml_report_customer_segment';
        $this->_headerText = __('Customer Segment Report');
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
