<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Source model for logging frequency
 */
namespace Magento\Logging\Model\Source;

class Frequency implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get options as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Daily')],
            ['value' => 7, 'label' => __('Weekly')],
            ['value' => 30, 'label' => __('Monthly')]
        ];
    }
}
