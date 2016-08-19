<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\WebsiteRestriction\Model\System\Config\Source;

/**
 * Sys config source model for stub page statuses
 *
 */
class Http extends \Magento\Framework\DataObject implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get options for select
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => \Magento\WebsiteRestriction\Model\Mode::HTTP_503,
                'label' => __('503 Service Unavailable'),
            ],
            ['value' => \Magento\WebsiteRestriction\Model\Mode::HTTP_200, 'label' => __('200 OK')]
        ];
    }
}
