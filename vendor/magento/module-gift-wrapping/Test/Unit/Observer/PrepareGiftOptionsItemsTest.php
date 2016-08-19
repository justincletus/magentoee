<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\GiftWrapping\Test\Unit\Observer;

class PrepareGiftOptionsItemsTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GiftWrapping\Observer\PrepareGiftOptionsItems */
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
                'getItems',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->_model = $objectManagerHelper->getObject('\Magento\GiftWrapping\Observer\PrepareGiftOptionsItems',
            [
                'giftWrappingData' =>  $this->helperDataMock
            ]);
        $this->_event = new \Magento\Framework\DataObject();
        $this->_observer = new \Magento\Framework\Event\Observer(['event' => $this->_event]);
    }


    public function testPrepareGiftOptionsItems()
    {
        $itemMock = $this->getMock('Magento\Framework\Object',
            [
                'getProduct',
                'getIsVirtual',
                'setIsGiftOptionsAvailable',
                '__wakeup'
            ],
            [],
            '',
            false);
        $productMock = $this->getMock('Magento\Catalog\Model\Product',
            [
                'getGiftWrappingAvailable',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getItems')->will($this->returnValue([$itemMock]));
        $itemMock->expects($this->once())->method('getProduct')->will($this->returnValue($productMock));
        $productMock->expects($this->once())->method('getGiftWrappingAvailable')->will($this->returnValue(true));
        $this->helperDataMock->expects($this->once())
            ->method('isGiftWrappingAvailableForProduct')->with(true)->will($this->returnValue(true));
        $itemMock->expects($this->once())->method('getIsVirtual')->will($this->returnValue(false));
        $itemMock->expects($this->once())->method('setIsGiftOptionsAvailable')->with(true);

        $this->_model->execute($this->observerMock);
    }

    public function testPrepareGiftOptionsItemsWithVirtualProduct()
    {
        $itemMock = $this->getMock('Magento\Framework\Object',
            [
                'getProduct',
                'getIsVirtual',
                'setIsGiftOptionsAvailable',
                '__wakeup'
            ],
            [],
            '',
            false);
        $productMock = $this->getMock('Magento\Catalog\Model\Product',
            [
                'getGiftWrappingAvailable',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getItems')->will($this->returnValue([$itemMock]));
        $itemMock->expects($this->once())->method('getProduct')->will($this->returnValue($productMock));
        $productMock->expects($this->once())->method('getGiftWrappingAvailable')->will($this->returnValue(true));
        $this->helperDataMock->expects($this->once())
            ->method('isGiftWrappingAvailableForProduct')->with(true)->will($this->returnValue(true));
        $itemMock->expects($this->once())->method('getIsVirtual')->will($this->returnValue(true));
        $itemMock->expects($this->never())->method('setIsGiftOptionsAvailable');

        $this->_model->execute($this->observerMock);
    }

    public function testPrepareGiftOptionsItemsWithNotAllowedProduct()
    {
        $itemMock = $this->getMock('Magento\Framework\Object',
            [
                'getProduct',
                'getIsVirtual',
                'setIsGiftOptionsAvailable',
                '__wakeup'
            ],
            [],
            '',
            false);
        $productMock = $this->getMock('Magento\Catalog\Model\Product',
            [
                'getGiftWrappingAvailable',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getItems')->will($this->returnValue([$itemMock]));
        $itemMock->expects($this->once())->method('getProduct')->will($this->returnValue($productMock));
        $productMock->expects($this->once())->method('getGiftWrappingAvailable')->will($this->returnValue(false));
        $this->helperDataMock->expects($this->once())
            ->method('isGiftWrappingAvailableForProduct')->with(false)->will($this->returnValue(false));
        $itemMock->expects($this->never())->method('getIsVirtual');
        $itemMock->expects($this->never())->method('setIsGiftOptionsAvailable');

        $this->_model->execute($this->observerMock);
    }
}
