<?php
/**
 * Configuration source for grant permission select
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Model\Adminhtml\System\Config\Source;

use Magento\CatalogPermissions\App\ConfigInterface;
use Magento\Framework\Option\ArrayInterface;

class Grant implements ArrayInterface
{
    /**
     * Retrieve Options Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ConfigInterface::GRANT_ALL => __('Yes, for Everyone'),
            ConfigInterface::GRANT_CUSTOMER_GROUP => __('Yes, for Specified Customer Groups'),
            ConfigInterface::GRANT_NONE => __('No')
        ];
    }
}
