<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Reward\Test\Unit\Observer;

class RevertRewardPointsForAllOrdersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $reverterMock;

    /**
     * @var \Magento\Reward\Observer\RevertRewardPointsForAllOrders
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->reverterMock = $this->getMock('\Magento\Reward\Model\Reward\Reverter', [], [], '', false);
        $this->subject = $objectManager->getObject('Magento\Reward\Observer\RevertRewardPointsForAllOrders',
            ['reverter' => $this->reverterMock]
        );
    }

    public function testRevertRewardPointsIfNoOrders()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrders'], [], '', false);
        $eventMock->expects($this->once())->method('getOrders')->will($this->returnValue([]));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testRevertRewardPoints()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $orderMock = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false);

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrders'], [], '', false);
        $eventMock->expects($this->once())->method('getOrders')->will($this->returnValue([$orderMock]));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $this->reverterMock->expects($this->once())
            ->method('revertRewardPointsForOrder')
            ->with($orderMock)
            ->will($this->returnSelf());

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
