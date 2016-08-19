<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCardAccount\Api;

interface GuestGiftCardAccountManagementInterface
{
    /**
     * @param string $cartId
     * @param \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface $giftCardAccountData
     * @return bool
     */
    public function addGiftCard(
        $cartId,
        \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface $giftCardAccountData
    );

    /**
     * @param string $cartId
     * @param string $giftCardCode
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return float
     */
    public function checkGiftCard($cartId, $giftCardCode);
}
