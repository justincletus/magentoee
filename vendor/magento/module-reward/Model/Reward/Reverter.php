<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Model\Reward;

class Reverter
{
    /**
     * Reward factory
     *
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Reward\Model\ResourceModel\RewardFactory
     */
    protected $rewardResourceFactory;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     * @param \Magento\Reward\Model\ResourceModel\RewardFactory $rewardResourceFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        \Magento\Reward\Model\ResourceModel\RewardFactory $rewardResourceFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->_rewardFactory = $rewardFactory;
        $this->rewardResourceFactory = $rewardResourceFactory;
    }

    /**
     * Revert authorized reward points amount for order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return $this
     */
    public function revertRewardPointsForOrder(\Magento\Sales\Model\Order $order)
    {
        if (!$order->getCustomerId()) {
            return $this;
        }
        $this->_rewardFactory->create()->setCustomerId(
            $order->getCustomerId()
        )->setWebsiteId(
            $this->_storeManager->getStore($order->getStoreId())->getWebsiteId()
        )->setPointsDelta(
            $order->getRewardPointsBalance()
        )->setAction(
            \Magento\Reward\Model\Reward::REWARD_ACTION_REVERT
        )->setActionEntity(
            $order
        )->updateRewardPoints();

        return $this;
    }

    /**
     * Revert sales rule earned reward points for order.
     *
     * @param \Magento\Sales\Model\Order $order
     * @return $this
     */
    public function revertEarnedRewardPointsForOrder(\Magento\Sales\Model\Order $order)
    {
        $appliedRuleIds = array_unique(explode(',', $order->getAppliedRuleIds()));
        /** @var $resource \Magento\Reward\Model\ResourceModel\Reward */
        $rewardRules = $this->rewardResourceFactory->create()->getRewardSalesrule($appliedRuleIds);
        $pointsDelta = array_sum(array_column($rewardRules, 'points_delta'));

        if ($pointsDelta && !$order->getCustomerIsGuest()) {
            $reward = $this->_rewardFactory->create();
            $reward->setCustomerId(
                $order->getCustomerId()
            )->setWebsiteId(
                $this->_storeManager->getStore($order->getStoreId())->getWebsiteId()
            )->setPointsDelta(
                -$pointsDelta
            )->setAction(
                \Magento\Reward\Model\Reward::REWARD_ACTION_REVERT
            )->setActionEntity(
                $order
            )->updateRewardPoints();
        }

        return $this;
    }
}
