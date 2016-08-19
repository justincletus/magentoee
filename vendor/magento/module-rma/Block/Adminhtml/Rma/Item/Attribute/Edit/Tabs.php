<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Item\Attribute\Edit;

/**
 * RMA items attributes edit page tabs
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Initialize edit tabs
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('rma_item_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attribute Information'));
    }
}
