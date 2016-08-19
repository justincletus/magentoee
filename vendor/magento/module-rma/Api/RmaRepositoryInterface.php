<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Api;

/**
 * Interface RmaRepositoryInterface
 * @api
 */
interface RmaRepositoryInterface
{
    /**
     * Return data object for specified RMA id
     *
     * @param int $id
     * @return \Magento\Rma\Api\Data\RmaInterface
     */
    public function get($id);

    /**
     * Return list of RMA data objects based on search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Rma\Api\Data\RmaSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria);

    /**
     * Save RMA
     *
     * @param \Magento\Rma\Api\Data\RmaInterface $rmaDataObject
     * @return \Magento\Rma\Api\Data\RmaInterface
     */
    public function save(\Magento\Rma\Api\Data\RmaInterface $rmaDataObject);

    /**
     * Delete RMA
     *
     * @param \Magento\Rma\Api\Data\RmaInterface $rmaDataObject
     * @return bool
     */
    public function delete(\Magento\Rma\Api\Data\RmaInterface $rmaDataObject);
}
