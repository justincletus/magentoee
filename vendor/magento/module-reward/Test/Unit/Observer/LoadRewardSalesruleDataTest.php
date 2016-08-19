<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Observer;

class LoadRewardSalesruleDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardResourceFactoryMock;

    /**
     * @var \Magento\Reward\Observer\InvitationToCustomer
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->rewardDataMock = $this->getMock('\Magento\Reward\Helper\Data', [], [], '', false);
        $this->rewardResourceFactoryMock = $this->getMock(
            '\Magento\Reward\Model\ResourceModel\RewardFactory',
            ['create', '__wakeup'],
            [],
            '',
            false
        );

        $this->subject = $objectManager->getObject(
            'Magento\Reward\Observer\LoadRewardSalesruleData',
            ['rewardData' => $this->rewardDataMock, 'rewardResourceFactory' => $this->rewardResourceFactoryMock]
        );
    }

    public function testSetRewardPointsIfRewardsDisabled()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->rewardDataMock->expects($this->once())->method('isEnabled')->will($this->returnValue(false));
        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testSetRewardPointsIfSalesruleIdIsNull()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $this->rewardDataMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));

        $ruleMock = $this->getMock('\Magento\SalesRule\Model\Rule', ['getId', '__wakeup'], [], '', false);
        $ruleMock->expects($this->once())->method('getId')->will($this->returnValue(null));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getRule'], [], '', false);
        $eventMock->expects($this->once())->method('getRule')->will($this->returnValue($ruleMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testSetRewardPointsIfPointsDeltaNotSet()
    {
        $ruleId = 10;
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $this->rewardDataMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));

        $ruleMock = $this->getMock('\Magento\SalesRule\Model\Rule', ['getId', '__wakeup'], [], '', false);
        $ruleMock->expects($this->exactly(2))->method('getId')->will($this->returnValue($ruleId));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getRule'], [], '', false);
        $eventMock->expects($this->once())->method('getRule')->will($this->returnValue($ruleMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $rewardResourceMock = $this->getMock('\Magento\Reward\Model\ResourceModel\Reward', [], [], '', false);
        $this->rewardResourceFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($rewardResourceMock));

        $rewardResourceMock->expects($this->once())
            ->method('getRewardSalesrule')
            ->with($ruleId)
            ->will($this->returnValue([]));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testSetRewardPointsSuccess()
    {
        $ruleId = 10;
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $this->rewardDataMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));

        $ruleMock = $this->getMock(
            '\Magento\SalesRule\Model\Rule',
            ['getId', '__wakeup', 'setRewardPointsDelta'],
            [],
            '',
            false
        );
        $ruleMock->expects($this->exactly(2))->method('getId')->will($this->returnValue($ruleId));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getRule'], [], '', false);
        $eventMock->expects($this->once())->method('getRule')->will($this->returnValue($ruleMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $rewardResourceMock = $this->getMock('\Magento\Reward\Model\ResourceModel\Reward', [], [], '', false);
        $this->rewardResourceFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($rewardResourceMock));

        $rewardResourceMock->expects($this->once())
            ->method('getRewardSalesrule')
            ->with($ruleId)
            ->will($this->returnValue(['points_delta' => 10]));

        $ruleMock->expects($this->once())
            ->method('setRewardPointsDelta')
            ->with(10)
            ->will($this->returnSelf());

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
