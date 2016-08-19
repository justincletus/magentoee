<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\GiftWrapping\Test\Unit\Observer;

class CheckoutProcessWrappingInfoTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GiftWrapping\Observer\CheckoutProcessWrappingInfo */
    protected $_model;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_event;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemInfoManager;

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
        $this->itemInfoManager = $this->getMock('Magento\GiftWrapping\Observer\ItemInfoManager', [], [], '', false);
        $this->observerMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $this->eventMock = $this->getMock('Magento\Framework\Event',
            [
                'getQuote',
                'getItems',
                'getOrder',
                'getOrderItem',
                'getQuoteItem',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->_model = $objectManagerHelper->getObject('\Magento\GiftWrapping\Observer\CheckoutProcessWrappingInfo',
            [
                'itemInfoManager' =>  $this->itemInfoManager
            ]);
        $this->_event = new \Magento\Framework\DataObject();
    }

    public function testCheckoutProcessWrappingInfoQuote()
    {
        $giftWrappingInfo = ['quote' => [1 => ['some data']]];
        $requestMock = $this->getMock('\Magento\Framework\App\RequestInterface', [], [], '', false);
        $quoteMock = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false);
        $event = new \Magento\Framework\Event(['request' => $requestMock, 'quote' => $quoteMock]);
        $observer = new \Magento\Framework\Event\Observer(['event' => $event]);

        $requestMock->expects($this->once())
            ->method('getParam')
            ->with('giftwrapping')
            ->will($this->returnValue($giftWrappingInfo));

        $this->itemInfoManager->expects($this->once())->method('saveOrderInfo')->with($quoteMock, ['some data'])
            ->willReturnSelf();
//        $quoteMock->expects($this->once())->method('getShippingAddress')->will($this->returnValue(false));
//        $quoteMock->expects($this->once())->method('addData')->will($this->returnSelf());
        $quoteMock->expects($this->never())->method('getAddressById');
        $this->_model->execute($observer);
    }
}
