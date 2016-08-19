<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Block\Ajax;

use Magento\Framework\View\Element\Template;

/**
 * Data block
 */
class Data extends Template
{
    /**
     * Get url for customer data ajax requests. Returns url with protocol matching used to request page.
     *
     * @param string $route
     * @return string Customer data url.
     */
    public function getCustomerDataUrl($route)
    {
        return $this->getUrl($route, ['_secure' => $this->getRequest()->isSecure()]);
    }
}
