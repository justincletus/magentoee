<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog Event data helper
 *
 */
namespace Magento\CatalogEvent\Helper;

use Magento\CatalogEvent\Model\Event;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'catalog/magento_catalogevent/enabled';

    /**
     * Retrieve event image url
     *
     * @param Event $event
     * @return string|false
     */
    public function getEventImageUrl($event)
    {
        if ($event->getImage()) {
            return $event->getImageUrl();
        }

        return false;
    }

    /**
     * Retrieve configuration value for enabled of catalog event
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
