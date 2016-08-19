<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Observer\PlaceOrder;

interface RestrictionInterface
{
    /**
     * Check if reward points operations is allowed
     *
     * @return bool
     */
    public function isAllowed();
}
