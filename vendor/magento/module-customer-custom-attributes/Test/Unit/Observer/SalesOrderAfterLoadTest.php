<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Test\Unit\Observer;

class SalesOrderAfterLoadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerCustomAttributes\Observer\SalesOrderAfterLoad
     */
    protected $observer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderFactory;

    public function setUp()
    {
        $this->orderFactory = $this->getMockBuilder(
            'Magento\CustomerCustomAttributes\Model\Sales\OrderFactory'
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();

        $this->observer = new \Magento\CustomerCustomAttributes\Observer\SalesOrderAfterLoad($this->orderFactory);
    }

    public function testSalesOrderAfterLoad()
    {
        $orderId = 1;
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getOrder'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->setMethods(['getId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $order = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Order')
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel->expects($this->once())->method('getId')->will($this->returnValue($orderId));
        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getOrder')->will($this->returnValue($dataModel));
        $order->expects($this->once())->method('load')->with($orderId)->will($this->returnSelf());
        $order->expects($this->once())->method('attachAttributeData')->with($dataModel)->will($this->returnSelf());
        $this->orderFactory->expects($this->once())->method('create')->will($this->returnValue($order));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Observer\SalesOrderAfterLoad',
            $this->observer->execute($observer)
        );
    }
}
