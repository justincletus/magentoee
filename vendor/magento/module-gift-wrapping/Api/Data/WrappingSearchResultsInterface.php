<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Api\Data;

interface WrappingSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items
     *
     * @return \Magento\GiftWrapping\Api\Data\WrappingInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Magento\GiftWrapping\Api\Data\WrappingInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
