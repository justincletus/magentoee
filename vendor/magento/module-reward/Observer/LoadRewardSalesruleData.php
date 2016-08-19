<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\SalesRule\Model\Rule;

class LoadRewardSalesruleData implements ObserverInterface
{
    /**
     * Reward factory
     *
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
     * Set reward points delta to salesrule model after it loaded
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
        if ($salesRule->getId()) {
            $data = $this->_rewardResourceFactory->create()->getRewardSalesrule($salesRule->getId());
            if (isset($data['points_delta'])) {
                $salesRule->setRewardPointsDelta($data['points_delta']);
            }
        }
        return $this;
    }
}
