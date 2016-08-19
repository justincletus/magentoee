<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Test\Unit\Model\Plugin;

// @codingStandardsIgnoreFile

use Magento\GiftWrapping\Helper\Data;
use Magento\GiftWrapping\Model\Plugin\MessageCartRepository;
use Magento\GiftWrapping\Model\WrappingFactory;

class MessageCartRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var MessageCartRepository */
    protected $model;

    /** @var \Magento\Quote\Api\CartRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $quoteRepositoryMock;

    /** @var WrappingFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $wrappingFactoryMock;

    /** @var Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $helperMock;

    protected function setUp()
    {
        $this->quoteRepositoryMock = $this->getMock('\Magento\Quote\Api\CartRepositoryInterface');
        $this->wrappingFactoryMock = $this->getMockBuilder('Magento\GiftWrapping\Model\WrappingFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMockForAbstractClass();

        $this->helperMock = $this->getMockBuilder('Magento\GiftWrapping\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = new MessageCartRepository(
            $this->quoteRepositoryMock,
            $this->wrappingFactoryMock,
            $this->helperMock
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testAroundSave()
    {
        $cartId = 135;
        $wrappingId = 23;
        $allowGiftReceipt = true;
        $addPrintedCard = true;
        $wrappingInfo = [
            'gw_id' => $wrappingId,
            'gw_allow_gift_receipt' => $allowGiftReceipt,
            'gw_add_card' => $addPrintedCard,
        ];

        $giftMessageMock = $this->getMockBuilder('\Magento\GiftMessage\Api\Data\MessageInterface')
            ->getMockForAbstractClass();

        $proceed = function ($internalCartId, $internalGiftMessage) use ($cartId, $giftMessageMock)
        {
            $this->assertEquals($cartId, $internalCartId);
            $this->assertEquals($giftMessageMock, $internalGiftMessage);
        };

        $this->helperMock->expects($this->once())
            ->method('isGiftWrappingAvailableForOrder')
            ->willReturn(true);
        $this->helperMock->expects($this->once())
            ->method('allowGiftReceipt')
            ->willReturn(true);
        $this->helperMock->expects($this->once())
            ->method('allowPrintedCard')
            ->willReturn(true);

        $extensionAttributesMock = $this->getMockBuilder('\Magento\GiftMessage\Api\Data\MessageExtensionInterface')
            ->setMethods(['getWrappingId', 'getWrappingAllowGiftReceipt', 'getWrappingAddPrintedCard'])
            ->getMockForAbstractClass();
        $extensionAttributesMock->expects($this->once())
            ->method('getWrappingId')
            ->willReturn($wrappingId);
        $extensionAttributesMock->expects($this->once())
            ->method('getWrappingAllowGiftReceipt')
            ->willReturn($allowGiftReceipt);
        $extensionAttributesMock->expects($this->once())
            ->method('getWrappingAddPrintedCard')
            ->willReturn($addPrintedCard);

        $giftMessageMock->expects($this->any())
            ->method('getExtensionAttributes')
            ->willReturn($extensionAttributesMock);

        $wrappingMock = $this->getMockBuilder('Magento\GiftWrapping\Model\Wrapping')
            ->disableOriginalConstructor()
            ->getMock();
        $wrappingMock->expects($this->any())
            ->method('load')
            ->with($wrappingId, null)
            ->willReturnSelf();
        $wrappingMock->expects($this->any())
            ->method('getId')
            ->willReturn($wrappingId);

        $this->wrappingFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($wrappingMock);

        $addressMock = $this->getMockBuilder('Magento\Quote\Model\Quote\Address')
            ->disableOriginalConstructor()
            ->getMock();
        $addressMock->expects($this->once())
            ->method('addData')
            ->with($wrappingInfo)
            ->willReturnSelf();

        $quoteMock = $this->getMockBuilder('Magento\Quote\Model\Quote')
            ->disableOriginalConstructor()
            ->getMock();
        $quoteMock->expects($this->once())
            ->method('addData')
            ->with($wrappingInfo)
            ->willReturnSelf();
        $quoteMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $quoteMock->expects($this->exactly(2))
            ->method('getShippingAddress')
            ->willReturn($addressMock);

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->willReturn($quoteMock);

        $cartRepositoryMock = $this->getMockBuilder('\Magento\GiftMessage\Api\CartRepositoryInterface')
            ->getMockForAbstractClass();

        $this->assertTrue($this->model->aroundSave($cartRepositoryMock, $proceed, $cartId, $giftMessageMock));
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testAroundSaveWithDisabledConfig()
    {
        $cartId = 135;

        $giftMessageMock = $this->getMockBuilder('\Magento\GiftMessage\Api\Data\MessageInterface')
            ->getMockForAbstractClass();

        $proceed = function ($internalCartId, $internalGiftMessage) use ($cartId, $giftMessageMock)
        {
            $this->assertEquals($cartId, $internalCartId);
            $this->assertEquals($giftMessageMock, $internalGiftMessage);
        };

        $this->helperMock->expects($this->once())
            ->method('isGiftWrappingAvailableForOrder')
            ->willReturn(false);
        $this->helperMock->expects($this->once())
            ->method('allowGiftReceipt')
            ->willReturn(false);
        $this->helperMock->expects($this->once())
            ->method('allowPrintedCard')
            ->willReturn(false);

        $this->helperMock->expects($this->once())
            ->method('isGiftWrappingAvailableForOrder')
            ->willReturn(false);
        $this->helperMock->expects($this->once())
            ->method('allowGiftReceipt')
            ->willReturn(false);
        $this->helperMock->expects($this->once())
            ->method('allowPrintedCard')
            ->willReturn(false);

        $extensionAttributesMock = $this->getMockBuilder('\Magento\GiftMessage\Api\Data\MessageExtensionInterface')
            ->getMockForAbstractClass();
        $giftMessageMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($extensionAttributesMock);

        $this->wrappingFactoryMock->expects($this->never())
            ->method('create');

        $quoteMock = $this->getMockBuilder('Magento\Quote\Model\Quote')
            ->disableOriginalConstructor()
            ->getMock();
        $quoteMock->expects($this->never())
            ->method('addData');
        $quoteMock->expects($this->never())
            ->method('save');
        $quoteMock->expects($this->never())
            ->method('getShippingAddress');

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->willReturn($quoteMock);

        $cartRepositoryMock = $this->getMockBuilder('\Magento\GiftMessage\Api\CartRepositoryInterface')
            ->getMockForAbstractClass();

        $this->assertTrue($this->model->aroundSave($cartRepositoryMock, $proceed, $cartId, $giftMessageMock));
    }
}
