<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\GiftWrapping\Test\Unit\Observer;

class SalesEventOrderItemToQuoteItemTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GiftWrapping\Observer\SalesEventOrderItemToQuoteItem */
    protected $_model;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_event;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventMock;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->helperDataMock = $this->getMock('Magento\GiftWrapping\Helper\Data', [], [], '', false);
        $this->observerMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $this->eventMock = $this->getMock('Magento\Framework\Event',
            [
                'getOrderItem',
                'getQuoteItem',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->_model = $objectManagerHelper->getObject('Magento\GiftWrapping\Observer\SalesEventOrderItemToQuoteItem',
            [
                'giftWrappingData' =>  $this->helperDataMock
            ]);
        $this->_event = new \Magento\Framework\DataObject();
        $this->_observer = new \Magento\Framework\Event\Observer(['event' => $this->_event]);
    }

    public function testSalesEventOrderItemToQuoteItemWithReorderedOrder()
    {
        $orderMock = $this->getMock('Magento\Sales\Model\Order',
            ['getStore', 'getReordered', '__wakeup'], [], '', false);
        $orderItemMock = $this->getMock('Magento\Sales\Model\Order\Item', ['getOrder', '__wakeup'], [], '', false);
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getOrderItem')->will($this->returnValue($orderItemMock));
        $orderItemMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $orderMock->expects($this->once())->method('getReordered')->will($this->returnValue(true));

        $this->_model->execute($this->observerMock);
    }

    public function testSalesEventOrderItemToQuoteItemWithGiftWrappingThatNotAllowedForItems()
    {
        $orderMock = $this->getMock('Magento\Sales\Model\Order',
            ['getStore', 'getReordered', '__wakeup'], [], '', false);
        $orderItemMock = $this->getMock('Magento\Sales\Model\Order\Item', ['getOrder', '__wakeup'], [], '', false);
        $storeMock = $this->getMock('Magento\Store\Model\Store', [], [], '', false);

        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getOrderItem')->will($this->returnValue($orderItemMock));
        $orderItemMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $orderMock->expects($this->once())->method('getReordered')->will($this->returnValue(false));

        $storeId = 12;
        $orderMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->helperDataMock->expects($this->once())
            ->method('isGiftWrappingAvailableForItems')
            ->with($storeId)
            ->will($this->returnValue(null));

        $this->_model->execute($this->observerMock);
    }

    public function testSalesEventOrderItemToQuoteItem()
    {
        $orderItemMock = $this->getMock('Magento\Sales\Model\Order\Item',
            [
                'getOrder',
                'getGwId',
                'getGwBasePrice',
                'getGwPrice',
                'getGwBaseTaxAmount',
                'getGwTaxAmount',
                '__wakeup'
            ],
            [],
            '',
            false);
        $quoteItemMock = $this->getMock('Magento\Quote\Model\Quote\Item',
            [
                'setGwId',
                'setGwBasePrice',
                'setGwPrice',
                'setGwBaseTaxAmount',
                'setGwTaxAmount',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getOrderItem')->will($this->returnValue($orderItemMock));
        $orderItemMock->expects($this->once())->method('getOrder')->will($this->returnValue(null));
        $this->eventMock->expects($this->once())->method('getQuoteItem')->will($this->returnValue($quoteItemMock));
        $orderItemMock->expects($this->once())->method('getGwId')->will($this->returnValue(11));
        $orderItemMock->expects($this->once())->method('getGwBasePrice')->will($this->returnValue(22));
        $orderItemMock->expects($this->once())->method('getGwPrice')->will($this->returnValue(33));
        $orderItemMock->expects($this->once())->method('getGwBaseTaxAmount')->will($this->returnValue(44));
        $orderItemMock->expects($this->once())->method('getGwTaxAmount')->will($this->returnValue(55));
        $quoteItemMock->expects($this->once())
            ->method('setGwId')
            ->with(11)
            ->will($this->returnValue($quoteItemMock));
        $quoteItemMock->expects($this->once())
            ->method('setGwBasePrice')
            ->with(22)
            ->will($this->returnValue($quoteItemMock));
        $quoteItemMock->expects($this->once())
            ->method('setGwPrice')
            ->with(33)
            ->will($this->returnValue($quoteItemMock));
        $quoteItemMock->expects($this->once())
            ->method('setGwBaseTaxAmount')
            ->with(44)->will($this->returnValue($quoteItemMock));
        $quoteItemMock->expects($this->once())
            ->method('setGwTaxAmount')
            ->with(55)
            ->will($this->returnValue($quoteItemMock));

        $this->_model->execute($this->observerMock);
    }
}
