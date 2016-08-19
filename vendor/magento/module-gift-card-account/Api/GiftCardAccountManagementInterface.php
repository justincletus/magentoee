<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCardAccount\Api;

/**
 * Interface GiftCardAccountManagementInterface
 */
interface GiftCardAccountManagementInterface
{
    /**
     * Remove GiftCard Account entity
     *
     * @param int $quoteId
     * @param string $giftCardCode
     * @return bool
     */
    public function deleteByQuoteId($quoteId, $giftCardCode);

    /**
     * Return GiftCard Account cards
     *
     * @param int $quoteId
     * @return \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function getListByQuoteId($quoteId);

    /**
     * @param int $cartId
     * @param \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface $giftCardAccountData
     * @return bool
     */
    public function saveByQuoteId(
        $cartId,
        \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface $giftCardAccountData
    );

    /**
     * @param int $cartId
     * @param string $giftCardCode
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return float
     */
    public function checkGiftCard($cartId, $giftCardCode);
}
