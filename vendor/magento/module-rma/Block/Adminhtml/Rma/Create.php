<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Block\Adminhtml\Rma;

/**
 * Admin RMA create
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Create extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_rma';
        $this->_mode = 'create';
        $this->_blockGroup = 'Magento_Rma';

        parent::_construct();

        $this->setId('magento_rma_rma_create');
        $this->removeButton('save');
        $this->removeButton('reset');
    }

    /**
     * Get header html
     *
     * @return string
     */
    public function getHeaderHtml()
    {
        return $this->getLayout()->createBlock('Magento\Rma\Block\Adminhtml\Rma\Create\Header')->toHtml();
    }
}
