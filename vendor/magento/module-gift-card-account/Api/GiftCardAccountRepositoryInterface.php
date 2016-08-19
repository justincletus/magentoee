<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardAccount\Api;

/**
 * Interface GiftCardAccountRepositoryInterface
 */
interface GiftCardAccountRepositoryInterface
{
    /**
     * Return data object for specified GiftCard Account id
     *
     * @param int $id
     * @return \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function get($id);

    /**
     * Return list of GiftCard Account data objects based on search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\GiftCardAccount\Api\Data\GiftCardAccountSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria);

    /**
     * Save GiftCard Account
     *
     * @param \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface $giftDataObject
     * @return \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface
     */
    public function save(\Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface $giftDataObject);

    /**
     * Delete GiftCard Account
     *
     * @param \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface $giftDataObject
     * @return bool
     */
    public function delete(\Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface $giftDataObject);
}
