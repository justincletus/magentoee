<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Gift wrapping options model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Model;

class Options extends \Magento\Framework\DataObject
{
    /**
     * Current data object
     */
    protected $_dataObject = null;

    /**
     * Set gift wrapping options data object
     *
     * @param \Magento\Framework\DataObject $item
     * @return \Magento\GiftWrapping\Model\Options
     */
    public function setDataObject($item)
    {
        if ($item instanceof \Magento\Framework\DataObject && $item->getGiftwrappingOptions()) {
            $this->addData(unserialize($item->getGiftwrappingOptions()));
            $this->_dataObject = $item;
        }
        return $this;
    }

    /**
     * Update gift wrapping options data object
     *
     * @return \Magento\GiftWrapping\Model\Options
     */
    public function update()
    {
        $this->_dataObject->setGiftwrappingOptions(serialize($this->getData()));
        return $this;
    }
}
