<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Reward\Test\Unit\Observer;

class CustomerSubscribedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardDataMock;

    /**
     * @var \Magento\Reward\Observer\CustomerSubscribed
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->rewardDataMock = $this->getMock('\Magento\Reward\Helper\Data', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->rewardFactoryMock = $this->getMock('\Magento\Reward\Model\RewardFactory', ['create'], [], '', false);

        $this->subject = $objectManager->getObject('Magento\Reward\Observer\CustomerSubscribed',
            [
                'rewardData' => $this->rewardDataMock,
                'storeManager' => $this->storeManagerMock,
                'rewardFactory' => $this->rewardFactoryMock
            ]
        );
    }

    public function testUpdateRewardsAfterSubscribtionIfSubscriberExist()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $subscriberMock = $this->getMock(
            '\Magento\Newsletter\Model\Subscriber',
            ['isObjectNew', '__wakeup'],
            [],
            '',
            false
        );

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getSubscriber'], [], '', false);
        $eventMock->expects($this->once())->method('getSubscriber')->will($this->returnValue($subscriberMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $subscriberMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(false));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testUpdateRewardsAfterSubscribtionIfCustomerNotExist()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $subscriberMock = $this->getMock(
            '\Magento\Newsletter\Model\Subscriber',
            ['isObjectNew', '__wakeup', 'getCustomerId'],
            [],
            '',
            false
        );

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getSubscriber'], [], '', false);
        $eventMock->expects($this->once())->method('getSubscriber')->will($this->returnValue($subscriberMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $subscriberMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(true));
        $subscriberMock->expects($this->once())->method('getCustomerId')->will($this->returnValue(null));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testUpdateRewardsAfterSubscribtionIfRewardDisabledOnFront()
    {
        $customerId = 10;
        $storeId = 2;
        $websiteId = 1;
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $subscriberMock = $this->getMock(
            '\Magento\Newsletter\Model\Subscriber',
            ['isObjectNew', '__wakeup', 'getCustomerId', 'getStoreId'],
            [],
            '',
            false
        );

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getSubscriber'], [], '', false);
        $eventMock->expects($this->once())->method('getSubscriber')->will($this->returnValue($subscriberMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $subscriberMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(true));
        $subscriberMock->expects($this->once())->method('getCustomerId')->will($this->returnValue($customerId));
        $subscriberMock->expects($this->once())->method('getStoreId')->will($this->returnValue($storeId));

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue($websiteId));
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->will($this->returnValue($storeMock));

        $this->rewardDataMock->expects($this->once())
            ->method('isEnabledOnFront')
            ->with($websiteId)
            ->will($this->returnValue(false));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testUpdateRewardsAfterSubscribtionSuccess()
    {
        $customerId = 10;
        $storeId = 2;
        $websiteId = 1;
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $subscriberMock = $this->getMock(
            '\Magento\Newsletter\Model\Subscriber',
            ['isObjectNew', '__wakeup', 'getCustomerId', 'getStoreId'],
            [],
            '',
            false
        );

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getSubscriber'], [], '', false);
        $eventMock->expects($this->once())->method('getSubscriber')->will($this->returnValue($subscriberMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $subscriberMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(true));
        $subscriberMock->expects($this->exactly(2))->method('getCustomerId')->will($this->returnValue($customerId));
        $subscriberMock->expects($this->exactly(2))->method('getStoreId')->will($this->returnValue($storeId));

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue($websiteId));
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->will($this->returnValue($storeMock));

        $this->rewardDataMock->expects($this->once())
            ->method('isEnabledOnFront')
            ->with($websiteId)
            ->will($this->returnValue(true));

        $rewardMock = $this->getMock(
            '\Magento\Reward\Model\Reward',
            ['setCustomerId', 'setActionEntity', 'setStore', 'setAction', 'updateRewardPoints', '__wakeup'],
            [],
            '',
            false
        );
        $this->rewardFactoryMock->expects($this->once())->method('create')->will($this->returnValue($rewardMock));

        $rewardMock->expects($this->once())->method('setCustomerId')->with($customerId)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setStore')->with($storeId)->will($this->returnSelf());
        $rewardMock->expects($this->once())
            ->method('setAction')
            ->with(\Magento\Reward\Model\Reward::REWARD_ACTION_NEWSLETTER)
            ->will($this->returnSelf());
        $rewardMock->expects($this->once())
            ->method('setActionEntity')
            ->with($subscriberMock)
            ->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('updateRewardPoints')->will($this->returnSelf());

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
