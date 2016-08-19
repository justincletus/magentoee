<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Api\Data;

/**
 * Interface TrackSearchResultInterface
 * @api
 */
interface TrackSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Rma list
     *
     * @return \Magento\Rma\Api\Data\TrackInterface[]
     */
    public function getItems();

    /**
     * Set Rma list
     *
     * @param \Magento\Rma\Api\Data\TrackInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
