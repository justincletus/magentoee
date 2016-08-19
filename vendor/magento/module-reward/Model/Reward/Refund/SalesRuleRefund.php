<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reward\Model\Reward\Refund;

class SalesRuleRefund
{
    /**
     * Reward factory
     *
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $rewardFactory;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Reward Helper
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $rewardHelper;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     * @param \Magento\Reward\Helper\Data $rewardHelper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        \Magento\Reward\Helper\Data $rewardHelper
    ) {
        $this->rewardFactory = $rewardFactory;
        $this->storeManager = $storeManager;
        $this->rewardHelper = $rewardHelper;
    }

    /**
     * Refund reward points earned by salesRule
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return void
     */
    public function refund(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $creditmemo->getOrder();

        if ($creditmemo->getAutomaticallyCreated()) {
            $creditmemo->setRewardPointsBalanceRefund($creditmemo->getRewardPointsBalance());
        }

        if ($this->isAllowedRefund($creditmemo)
            && $order->getRewardSalesrulePoints() > 0
            && $order->getTotalQtyOrdered() - $this->getTotalItemsToRefund($creditmemo, $order) == 0
        ) {
            $rewardModel = $this->getRewardModel([
                'website_id' => $this->storeManager->getStore($order->getStoreId())->getWebsiteId(),
                'customer_id' => $order->getCustomerId(),
                'points_delta' => (-$this->getRewardPointsToVoid($order)),
                'action' => \Magento\Reward\Model\Reward::REWARD_ACTION_CREDITMEMO_VOID,
            ]);
            $rewardModel->setActionEntity($order);
            $rewardModel->save();
        }
    }

    /**
     * Return reward points qty to void
     *
     * @param \Magento\Sales\Model\Order $order
     * @return int
     */
    protected function getRewardPointsToVoid(\Magento\Sales\Model\Order $order)
    {
        $rewardModel = $this->getRewardModel([
            'website_id' => $this->storeManager->getStore($order->getStoreId())->getWebsiteId(),
            'customer_id' => $order->getCustomerId(),
        ]);
        $rewardModel->loadByCustomer();

        if ($rewardModel->getPointsBalance() >= $order->getRewardSalesrulePoints()) {
            return (int)$order->getRewardSalesrulePoints();
        }
        return (int)$rewardModel->getPointsBalance();
    }

    /**
     * Return is refund allowed for creditmemo
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return bool
     */
    protected function isAllowedRefund(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        return $creditmemo->getAutomaticallyCreated() ? $this->rewardHelper->isAutoRefundEnabled() : true;
    }

    /**
     * Return total items to refund
     * Sum of all creditmemo items
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param \Magento\Sales\Model\Order $order
     * @return int
     */
    protected function getTotalItemsToRefund(
        \Magento\Sales\Model\Order\Creditmemo $creditmemo,
        \Magento\Sales\Model\Order $order
    ) {
        $totalItemsRefund = $creditmemo->getTotalQty();
        foreach ($order->getCreditmemosCollection() as $creditMemo) {
            foreach ($creditMemo->getAllItems() as $item) {
                $totalItemsRefund += $item->getQty();
            }
        }
        return (int)$totalItemsRefund;
    }

    /**
     * Return reward model
     *
     * @param array $data
     * @return \Magento\Reward\Model\Reward
     */
    protected function getRewardModel($data = [])
    {
        return $this->rewardFactory->create(['data' => $data]);
    }
}
