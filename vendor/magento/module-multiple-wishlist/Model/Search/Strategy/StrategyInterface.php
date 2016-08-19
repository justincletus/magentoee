<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Wishlist search strategy interface
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Model\Search\Strategy;

interface StrategyInterface
{
    /**
     * Filter given wishlist collection
     *
     * @abstract
     * @param \Magento\Wishlist\Model\ResourceModel\Wishlist\Collection $collection
     * @return \Magento\Wishlist\Model\ResourceModel\Wishlist\Collection
     */
    public function filterCollection(\Magento\Wishlist\Model\ResourceModel\Wishlist\Collection $collection);
}
