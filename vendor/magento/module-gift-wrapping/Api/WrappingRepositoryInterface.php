<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Api;

/**
 * Interface WrappingRepositoryInterface
 * @api
 */
interface WrappingRepositoryInterface
{
    /**
     * Return data object for specified wrapping ID and store.
     *
     * @param int $id
     * @param int $storeId
     * @return \Magento\GiftWrapping\Api\Data\WrappingInterface
     */
    public function get($id, $storeId = null);

    /**
     * Return list of gift wrapping data objects based on search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\GiftWrapping\Api\Data\WrappingSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria);

    /**
     * Create/Update new gift wrapping with data object values
     *
     * @param \Magento\GiftWrapping\Api\Data\WrappingInterface $data
     * @param int $storeId
     * @return \Magento\GiftWrapping\Api\Data\WrappingInterface
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     */
    public function save(\Magento\GiftWrapping\Api\Data\WrappingInterface $data, $storeId = null);

    /**
     * Delete gift wrapping
     *
     * @param \Magento\GiftWrapping\Api\Data\WrappingInterface $data
     * @return bool
     */
    public function delete(\Magento\GiftWrapping\Api\Data\WrappingInterface $data);

    /**
     * Delete gift wrapping
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     */
    public function deleteById($id);
}
