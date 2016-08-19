<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Invitation\Block\Adminhtml\Report\Invitation;

/**
 * Backend invitation general report page content block
 *
 */
class General extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_report_invitation_general';
        $this->_blockGroup = 'Magento_Invitation';
        $this->_headerText = __('General');
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
