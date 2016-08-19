<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\SalesRule\Model\Rule;

class SaveRewardSalesruleData implements ObserverInterface
{
    /**
     * Reward factory
     * @var \Magento\Reward\Model\ResourceModel\RewardFactory
     */
    protected $_rewardResourceFactory;

    /**
     * Reward helper
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Reward\Model\ResourceModel\RewardFactory $rewardResourceFactory
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Reward\Model\ResourceModel\RewardFactory $rewardResourceFactory
    ) {
        $this->_rewardData = $rewardData;
        $this->_rewardResourceFactory = $rewardResourceFactory;
    }

    /**
     * Save reward points delta for salesrule
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_rewardData->isEnabled()) {
            return $this;
        }
        /* @var $salesRule Rule */
        $salesRule = $observer->getEvent()->getRule();
        $this->_rewardResourceFactory->create()->saveRewardSalesrule(
            $salesRule->getId(),
            (int)$salesRule->getRewardPointsDelta()
        );
        return $this;
    }
}
