<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomAttributeManagement\Block\Form\Renderer;

/**
 * EAV Entity Attribute Form Renderer Block for Boolean
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Boolean extends \Magento\CustomAttributeManagement\Block\Form\Renderer\Select
{
    /**
     * Return array of select options
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            ['value' => '', 'label' => ''],
            ['value' => '0', 'label' => __('No')],
            ['value' => '1', 'label' => __('Yes')]
        ];
    }
}
