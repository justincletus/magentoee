<?php
/**
 * Configuration source for grant permission select
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Model\Adminhtml\System\Config\Source\Grant;

use Magento\CatalogPermissions\App\ConfigInterface;
use Magento\Framework\Option\ArrayInterface;

class Landing implements ArrayInterface
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
            ConfigInterface::GRANT_NONE => __('No, Redirect to Landing Page')
        ];
    }
}
