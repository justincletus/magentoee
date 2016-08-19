<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\WebsiteRestriction\Model\System\Config\Source;

/**
 * Sys config source model for private sales redirect modes
 *
 */
class Redirect extends \Magento\Framework\DataObject implements \Magento\Framework\Option\ArrayInterface
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
                'value' => \Magento\WebsiteRestriction\Model\Mode::HTTP_302_LOGIN,
                'label' => __('To login form (302 Found)'),
            ],
            [
                'value' => \Magento\WebsiteRestriction\Model\Mode::HTTP_302_LANDING,
                'label' => __('To landing page (302 Found)')
            ]
        ];
    }
}
