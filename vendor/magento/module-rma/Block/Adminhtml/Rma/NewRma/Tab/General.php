<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * General Tab in New RMA form
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\NewRma\Tab;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class General extends \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General
{
    /**
     * Create form. Fieldset are being added in child blocks
     *
     * @return \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $htmlIdPrefix = 'rma_properties_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $this->setForm($form);
        return $this;
    }
}
