<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Block\Adminhtml;

class Banner extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize banners manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_banner';
        $this->_blockGroup = 'Magento_Banner';
        $this->_headerText = __('Banners');
        $this->_addButtonLabel = __('Add Banner');
        parent::_construct();
    }
}
