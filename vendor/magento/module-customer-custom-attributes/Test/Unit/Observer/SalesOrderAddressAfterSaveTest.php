<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Test\Unit\Observer;

class SalesOrderAddressAfterSaveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerCustomAttributes\Observer\SalesOrderAddressAfterSave
     */
    protected $observer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderAddressFactory;

    public function setUp()
    {
        $this->orderAddressFactory = $this->getMockBuilder(
            'Magento\CustomerCustomAttributes\Model\Sales\Order\AddressFactory'
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();

        $this->observer = new \Magento\CustomerCustomAttributes\Observer\SalesOrderAddressAfterSave(
            $this->orderAddressFactory
        );
    }

    public function testSalesOrderAddressAfterSave()
    {
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getAddress'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->setMethods(['__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $orderAddress = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Quote\Address')
            ->disableOriginalConstructor()
            ->getMock();

        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getAddress')->will($this->returnValue($dataModel));
        $orderAddress->expects($this->once())->method('saveAttributeData')->with($dataModel)->will($this->returnSelf());
        $this->orderAddressFactory->expects($this->once())->method('create')->will($this->returnValue($orderAddress));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Observer\SalesOrderAddressAfterSave',
            $this->observer->execute($observer)
        );
    }
}
