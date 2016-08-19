<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Reward\Test\Unit\Observer;

class ReturnRewardPointsTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\Reward\Observer\ReturnRewardPoints
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->rewardFactoryMock = $this->getMock('\Magento\Reward\Model\RewardFactory', ['create'], [], '', false);
        $this->subject = $objectManager->getObject('Magento\Reward\Observer\ReturnRewardPoints',
            ['storeManager' => $this->storeManagerMock, 'rewardFactory' => $this->rewardFactoryMock]
        );
    }

    public function testReturnRewardPointsIfPointsBalanceIsZero()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['getRewardPointsBalance', '__wakeup'],
            [],
            '',
            false
        );
        $orderMock->expects($this->once())->method('getRewardPointsBalance')->will($this->returnValue(0));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrder'], [], '', false);
        $eventMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testReturnRewardPoints()
    {
        $customerId = 100;
        $storeId = 1;
        $websiteId = 2;
        $pointsBalance = 100;

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['getRewardPointsBalance', '__wakeup', 'getCustomerId', 'getStoreId'],
            [],
            '',
            false
        );
        $orderMock->expects($this->exactly(2))
            ->method('getRewardPointsBalance')
            ->will($this->returnValue($pointsBalance));
        $orderMock->expects($this->once())->method('getCustomerId')->will($this->returnValue($customerId));
        $orderMock->expects($this->once())->method('getStoreId')->will($this->returnValue($storeId));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrder'], [], '', false);
        $eventMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $rewardMock = $this->getMock(
            '\Magento\Reward\Model\Reward',
            [
                'setCustomerId',
                'setActionEntity',
                'setWebsiteId',
                'setAction',
                'updateRewardPoints',
                '__wakeup',
                'setPointsDelta'
            ],
            [],
            '',
            false
        );
        $this->rewardFactoryMock->expects($this->once())->method('create')->will($this->returnValue($rewardMock));

        $rewardMock->expects($this->once())->method('setCustomerId')->with($customerId)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setWebsiteId')->with($websiteId)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setPointsDelta')->with($pointsBalance)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setActionEntity')->with($orderMock)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('updateRewardPoints')->will($this->returnSelf());
        $rewardMock->expects($this->once())
            ->method('setAction')
            ->with(\Magento\Reward\Model\Reward::REWARD_ACTION_REVERT)
            ->will($this->returnSelf());

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue($websiteId));
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->will($this->returnValue($storeMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
