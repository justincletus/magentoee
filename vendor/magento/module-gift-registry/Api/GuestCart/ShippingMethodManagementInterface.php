<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftRegistry\Api\GuestCart;

interface ShippingMethodManagementInterface
{
    /**
     * Estimate shipping
     *
     * @param string $cartId The shopping cart ID.
     * @param int $registryId The estimate registry id
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[] An array of shipping methods.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function estimateByRegistryId($cartId, $registryId);
}
