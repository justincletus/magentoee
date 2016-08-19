<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Rma Item Form Renderer Block for select
 */
namespace Magento\Rma\Block\Form\Renderer;

class Image extends \Magento\CustomAttributeManagement\Block\Form\Renderer\Image
{
    /**
     * Gets image url path
     *
     * @return string
     */
    public function getImageUrl()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . \Magento\Rma\Model\Item::ITEM_IMAGE_URL;
        $file = $this->getValue();
        $url = $url . $file;
        return $url;
    }
}
