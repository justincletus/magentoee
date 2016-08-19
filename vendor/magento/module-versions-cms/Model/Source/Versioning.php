<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Model\Source;

/**
 * Versioning configuration source model
 *
 */
class Versioning implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return ['1' => __('Enabled by Default'), '1' => __('Disabled by Default')];
    }
}
