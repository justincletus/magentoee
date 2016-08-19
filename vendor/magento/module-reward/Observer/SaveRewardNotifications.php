<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class SaveRewardNotifications
 */
class SaveRewardNotifications implements ObserverInterface
{
    /**
     * Reward helper
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData
    ) {
        $this->_rewardData = $rewardData;
    }

    /**
     * Update reward notifications for customer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_rewardData->isEnabled()) {
            return $this;
        }

        $request = $observer->getEvent()->getRequest();
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $observer->getEvent()->getCustomer();

        $data = $request->getPost('reward');
        // If new customer
        if (!$customer->getId()) {
            $subscribeByDefault = (int)$this->_rewardData->getNotificationConfig(
                'subscribe_by_default',
                (int)$customer->getWebsiteId()
            );
            $data['reward_update_notification'] = $subscribeByDefault;
            $data['reward_warning_notification'] = $subscribeByDefault;
        }

        $customer->setCustomAttribute(
            'reward_update_notification',
            empty($data['reward_update_notification']) ? 0 : 1
        );
        $customer->setCustomAttribute(
            'reward_warning_notification',
            empty($data['reward_warning_notification']) ? 0 : 1
        );

        return $this;
    }
}
