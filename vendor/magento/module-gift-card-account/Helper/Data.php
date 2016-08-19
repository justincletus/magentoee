<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardAccount\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Maximal gift card code length according to database table definitions (longer codes are truncated)
     */
    const GIFT_CARD_CODE_MAX_LENGTH = 255;

    /**
     * Unserialize and return gift card list from specified object
     *
     * @param \Magento\Framework\DataObject $from
     * @return mixed
     */
    public function getCards(\Magento\Framework\DataObject $from)
    {
        $value = $from->getGiftCards();
        if (!$value) {
            return [];
        }
        return unserialize($value);
    }

    /**
     * Serialize and set gift card list to specified object
     *
     * @param \Magento\Framework\DataObject $to
     * @param mixed $value
     * @return void
     */
    public function setCards(\Magento\Framework\DataObject $to, $value)
    {
        $serializedValue = serialize($value);
        $to->setGiftCards($serializedValue);
    }
}
