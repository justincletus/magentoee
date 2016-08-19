<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Observer;

class SetRewardPointsBalanceToRefundTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Observer\SetRewardPointsBalanceToRefund
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->subject = $objectManager->getObject('Magento\Reward\Observer\SetRewardPointsBalanceToRefund');
    }

    public function testSetRewardPointsBalanceIfPointsBalanceInNull()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $creditmemoMock = $this->getMock('Magento\Sales\Model\Order\Creditmemo', [], [], '', false);

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getInput', 'getCreditmemo'], [], '', false);
        $eventMock->expects($this->once())->method('getInput')->will($this->returnValue([]));
        $eventMock->expects($this->once())->method('getCreditmemo')->will($this->returnValue($creditmemoMock));

        $observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($eventMock));
        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testSetRewardPointsBalanceIfRewardsRefundNotSet()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $creditmemoMock = $this->getMock('Magento\Sales\Model\Order\Creditmemo', [], [], '', false);

        $inputData = ['refund_reward_points' => 100];

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getInput', 'getCreditmemo'], [], '', false);
        $eventMock->expects($this->once())->method('getInput')->will($this->returnValue($inputData));
        $eventMock->expects($this->once())->method('getCreditmemo')->will($this->returnValue($creditmemoMock));

        $observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($eventMock));
        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testSetRewardPointsBalanceIfRewardsDisabled()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $creditmemoMock = $this->getMock('\Magento\Sales\Model\Order\Creditmemo', [], [], '', false);

        $inputData = [
            'refund_reward_points' => 100,
            'refund_reward_points_enable' => false,
        ];

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getInput', 'getCreditmemo'], [], '', false);
        $eventMock->expects($this->once())->method('getInput')->will($this->returnValue($inputData));
        $eventMock->expects($this->once())->method('getCreditmemo')->will($this->returnValue($creditmemoMock));

        $observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($eventMock));
        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testSetRewardPointsBalanceIfCreditMemoRewardsBalanceIsZero()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $creditmemoMock = $this->getMock(
            'Magento\Sales\Model\Order\Creditmemo',
            ['getRewardPointsBalance', '__wakeup'],
            [],
            '',
            false
        );

        $inputData = [
            'refund_reward_points' => 100,
            'refund_reward_points_enable' => true,
        ];

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getInput', 'getCreditmemo'], [], '', false);
        $eventMock->expects($this->once())->method('getInput')->will($this->returnValue($inputData));
        $eventMock->expects($this->once())->method('getCreditmemo')->will($this->returnValue($creditmemoMock));

        $observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($eventMock));
        $creditmemoMock->expects($this->once())->method('getRewardPointsBalance')->will($this->returnValue(0));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testSetRewardPointsBalanceSuccess()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $creditmemoMock = $this->getMock(
            'Magento\Sales\Model\Order\Creditmemo',
            ['getRewardPointsBalance', '__wakeup', 'setRewardPointsBalanceRefund'],
            [],
            '',
            false
        );

        $inputData = [
            'refund_reward_points' => 100,
            'refund_reward_points_enable' => true,
        ];

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getInput', 'getCreditmemo'], [], '', false);
        $eventMock->expects($this->once())->method('getInput')->will($this->returnValue($inputData));
        $eventMock->expects($this->once())->method('getCreditmemo')->will($this->returnValue($creditmemoMock));
        $observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($eventMock));

        $creditmemoMock->expects($this->once())->method('getRewardPointsBalance')->will($this->returnValue(50));
        $creditmemoMock->expects($this->once())
            ->method('setRewardPointsBalanceRefund')
            ->with(50)
            ->will($this->returnSelf());

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
