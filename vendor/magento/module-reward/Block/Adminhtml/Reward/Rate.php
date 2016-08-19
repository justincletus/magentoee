<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Block\Adminhtml\Reward;

/**
 * Reward rate grid container
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @codeCoverageIgnore
 */
class Rate extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Block constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Reward';
        $this->_controller = 'adminhtml_reward_rate';
        $this->_headerText = __('Reward Exchange Rates');
        parent::_construct();
        $this->buttonList->update('add', 'label', __('Add New Rate'));
    }
}
