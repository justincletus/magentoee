<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reward\Test\Unit\Observer;

class RevertRewardPointsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $reverterMock;

    /**
     * @var \Magento\Reward\Observer\RevertRewardPoints
     */
    protected $model;

    protected function setUp()
    {
        $this->reverterMock = $this->getMock('\Magento\Reward\Model\Reward\Reverter', [], [], '', false);
        $this->model = new \Magento\Reward\Observer\RevertRewardPoints($this->reverterMock);
    }

    public function testRevertRewardPointsIfOrderIsNull()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrder'], [], '', false);
        $eventMock->expects($this->once())->method('getOrder')->will($this->returnValue(null));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $this->assertEquals($this->model, $this->model->execute($observerMock));
    }

    public function testRevertRewardPoints()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $orderMock = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false);

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrder'], [], '', false);
        $eventMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $this->reverterMock->expects($this->once())
            ->method('revertRewardPointsForOrder')
            ->with($orderMock)
            ->will($this->returnSelf());
        $this->reverterMock->expects($this->once())->method('revertEarnedRewardPointsForOrder')->with($orderMock)
            ->willReturnSelf();

        $this->assertEquals($this->model, $this->model->execute($observerMock));
    }
}
