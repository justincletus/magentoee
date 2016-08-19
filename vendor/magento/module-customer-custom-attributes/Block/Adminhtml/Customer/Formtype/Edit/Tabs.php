<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype\Edit;

/**
 * Fort Type Edit Tabs Block
 *
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Initialize edit tabs
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('magento_customercustomattributes_formtype_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Form Type Information'));
    }
}
