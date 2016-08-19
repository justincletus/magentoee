<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Api;

/**
 * Interface TrackRepositoryInterface
 * @api
 */
interface TrackRepositoryInterface
{
    /**
     * Get Track by id
     *
     * @param int $id
     * @return \Magento\Rma\Api\Data\TrackInterface
     */
    public function get($id);

    /**
     * Return list of track data objects based on search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $criteria
     * @return \Magento\Rma\Api\Data\TrackSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $criteria);

    /**
     * Save Track
     *
     * @param \Magento\Rma\Api\Data\TrackInterface $track
     * @return bool
     */
    public function save(\Magento\Rma\Api\Data\TrackInterface $track);

    /**
     * Delete Track
     *
     * @param \Magento\Rma\Api\Data\TrackInterface $track
     * @return bool
     */
    public function delete(\Magento\Rma\Api\Data\TrackInterface $track);
}
