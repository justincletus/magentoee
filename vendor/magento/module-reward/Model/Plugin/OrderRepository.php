<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Model\Plugin;

use Magento\Sales\Model\Order;

class OrderRepository
{
    /**
     * Check if credit memo can be created for order with reward points
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param callable $proceed
     * @param int $orderId
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Closure $proceed,
        $orderId
    ) {
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $proceed($orderId);

        if ($order->canUnhold() || $order->isCanceled() || $order->getState() === Order::STATE_CLOSED) {
            return $order;
        }

        if ($order->getBaseRwrdCrrncyAmtInvoiced() > $order->getBaseRwrdCrrncyAmntRefnded()) {
            $order->setForcedCanCreditmemo(true);
        }

        return $order;
    }
}
