<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Test\Unit\Model\Plugin;

// @codingStandardsIgnoreFile

use Magento\GiftWrapping\Helper\Data;
use Magento\GiftWrapping\Model\Plugin\MessageItemRepository;
use Magento\GiftWrapping\Model\WrappingFactory;

class MessageItemRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var MessageItemRepository */
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

        $this->model = new MessageItemRepository(
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
        $itemId = 432;

        $giftMessageMock = $this->getMockBuilder('\Magento\GiftMessage\Api\Data\MessageInterface')
            ->getMockForAbstractClass();

        $proceed = function ($internalCartId, $internalGiftMessage, $internalItemId)
            use ($cartId, $giftMessageMock, $itemId)
        {
            $this->assertEquals($cartId, $internalCartId);
            $this->assertEquals($giftMessageMock, $internalGiftMessage);
            $this->assertEquals($itemId, $internalItemId);
        };

        $this->helperMock->expects($this->once())
            ->method('isGiftWrappingAvailableForItems')
            ->willReturn(true);

        $extensionAttributesMock = $this->getMockBuilder('\Magento\GiftMessage\Api\Data\MessageExtensionInterface')
            ->setMethods(['getWrappingId'])
            ->getMockForAbstractClass();
        $extensionAttributesMock->expects($this->once())
            ->method('getWrappingId')
            ->willReturn($wrappingId);

        $giftMessageMock->expects($this->once())
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

        $itemMock = $this->getMockBuilder('Magento\Quote\Model\Quote\Item')
            ->setMethods(['setGwId', 'save'])
            ->disableOriginalConstructor()
            ->getMock();
        $itemMock->expects($this->once())
            ->method('setGwId')
            ->with($wrappingId)
            ->willReturnSelf();
        $itemMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();

        $quoteMock = $this->getMockBuilder('Magento\Quote\Model\Quote')
            ->disableOriginalConstructor()
            ->getMock();
        $quoteMock->expects($this->once())
            ->method('getItemById')
            ->with($itemId)
            ->willReturn($itemMock);
        $quoteMock->expects($this->never())
            ->method('save');

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->willReturn($quoteMock);

        $cartRepositoryMock = $this->getMockBuilder('\Magento\GiftMessage\Api\ItemRepositoryInterface')
            ->getMockForAbstractClass();

        $this->assertTrue($this->model->aroundSave($cartRepositoryMock, $proceed, $cartId, $giftMessageMock, $itemId));
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testAroundSaveWithDisabledConfig()
    {
        $cartId = 135;
        $itemId = 432;

        $giftMessageMock = $this->getMockBuilder('\Magento\GiftMessage\Api\Data\MessageInterface')
            ->getMockForAbstractClass();

        $proceed = function ($internalCartId, $internalGiftMessage, $internalItemId)
            use ($cartId, $giftMessageMock, $itemId)
        {
            $this->assertEquals($cartId, $internalCartId);
            $this->assertEquals($giftMessageMock, $internalGiftMessage);
            $this->assertEquals($itemId, $internalItemId);
        };

        $this->helperMock->expects($this->once())
            ->method('isGiftWrappingAvailableForItems')
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
            ->method('getItemById');
        $quoteMock->expects($this->never())
            ->method('save');

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->willReturn($quoteMock);

        $cartRepositoryMock = $this->getMockBuilder('\Magento\GiftMessage\Api\ItemRepositoryInterface')
            ->getMockForAbstractClass();

        $this->assertTrue($this->model->aroundSave($cartRepositoryMock, $proceed, $cartId, $giftMessageMock, $itemId));
    }
}
