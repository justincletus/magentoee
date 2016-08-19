<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Reward\Test\Unit\Observer;

class InvitationToCustomerTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\Reward\Observer\InvitationToCustomer
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->rewardDataMock = $this->getMock('\Magento\Reward\Helper\Data', ['isEnabledOnFront'], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->rewardFactoryMock = $this->getMock('\Magento\Reward\Model\RewardFactory', ['create'], [], '', false);

        $this->subject = $objectManager->getObject('Magento\Reward\Observer\InvitationToCustomer',
            [
                'rewardData' => $this->rewardDataMock,
                'storeManager' => $this->storeManagerMock,
                'rewardFactory' => $this->rewardFactoryMock
            ]
        );
    }

    public function testUpdateRewardsIfRewardsDisabledOnFront()
    {
        $storeId = 1;
        $websiteId = 2;
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $invitationMock = $this->getMock(
            '\Magento\Invitation\Model\Invitation',
            ['getStoreId', '__wakeup'],
            [],
            '',
            false
        );

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getInvitation'], [], '', false);
        $eventMock->expects($this->once())->method('getInvitation')->will($this->returnValue($invitationMock));
        $invitationMock->expects($this->once())->method('getStoreId')->will($this->returnValue($storeId));

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

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testUpdateRewardsIfCustomerIdNotSet()
    {
        $storeId = 1;
        $websiteId = 2;
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $invitationMock = $this->getMock(
            '\Magento\Invitation\Model\Invitation',
            ['getStoreId', '__wakeup', 'getCustomerId'],
            [],
            '',
            false
        );

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getInvitation'], [], '', false);
        $eventMock->expects($this->once())->method('getInvitation')->will($this->returnValue($invitationMock));
        $invitationMock->expects($this->once())->method('getStoreId')->will($this->returnValue($storeId));

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

        $invitationMock->expects($this->once())->method('getCustomerId')->will($this->returnValue(null));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testUpdateRewardsIfReferralIdNotSet()
    {
        $customerId = 100;
        $storeId = 1;
        $websiteId = 2;
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $invitationMock = $this->getMock(
            '\Magento\Invitation\Model\Invitation',
            ['getStoreId', '__wakeup', 'getCustomerId', 'getReferralId'],
            [],
            '',
            false
        );

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getInvitation'], [], '', false);
        $eventMock->expects($this->once())->method('getInvitation')->will($this->returnValue($invitationMock));
        $invitationMock->expects($this->once())->method('getStoreId')->will($this->returnValue($storeId));

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

        $invitationMock->expects($this->once())->method('getCustomerId')->will($this->returnValue($customerId));
        $invitationMock->expects($this->once())->method('getReferralId')->will($this->returnValue(null));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testUpdateRewardsSuccess()
    {
        $customerId = 100;
        $storeId = 1;
        $websiteId = 2;
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $invitationMock = $this->getMock(
            '\Magento\Invitation\Model\Invitation',
            ['getStoreId', '__wakeup', 'getCustomerId', 'getReferralId'],
            [],
            '',
            false
        );

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getInvitation'], [], '', false);
        $eventMock->expects($this->once())->method('getInvitation')->will($this->returnValue($invitationMock));
        $invitationMock->expects($this->once())->method('getStoreId')->will($this->returnValue($storeId));

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

        $invitationMock->expects($this->exactly(2))->method('getCustomerId')->will($this->returnValue($customerId));
        $invitationMock->expects($this->once())->method('getReferralId')->will($this->returnValue(200));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $rewardMock = $this->getMock(
            '\Magento\Reward\Model\Reward',
            ['setCustomerId', 'setActionEntity', 'setWebsiteId', 'setAction', 'updateRewardPoints', '__wakeup'],
            [],
            '',
            false
        );
        $this->rewardFactoryMock->expects($this->once())->method('create')->will($this->returnValue($rewardMock));

        $rewardMock->expects($this->once())->method('setCustomerId')->with($customerId)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setWebsiteId')->with($websiteId)->will($this->returnSelf());
        $rewardMock->expects($this->once())
            ->method('setAction')
            ->with(\Magento\Reward\Model\Reward::REWARD_ACTION_INVITATION_CUSTOMER)
            ->will($this->returnSelf());
        $rewardMock->expects($this->once())
            ->method('setActionEntity')
            ->with($invitationMock)
            ->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('updateRewardPoints')->will($this->returnSelf());

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
