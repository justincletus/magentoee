<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Api;

interface RewardManagementInterface
{
    /**
     * Set reward points to quote
     *
     * @param int $cartId
     * @return boolean
     */
    public function set($cartId);
}
