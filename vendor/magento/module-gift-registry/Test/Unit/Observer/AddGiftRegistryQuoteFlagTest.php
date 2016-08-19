<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Test\Unit\Observer;

class AddGiftRegistryQuoteFlagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Observer\AddGiftRegistryQuoteFlag
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataMock;

    protected function setUp()
    {
        $this->dataMock = $this->getMock('\Magento\GiftRegistry\Helper\Data', [], [], '', false);
        $this->model = new \Magento\GiftRegistry\Observer\AddGiftRegistryQuoteFlag($this->dataMock);
    }

    public function testexecuteIfRegistryDisabled()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->dataMock->expects($this->once())->method('isEnabled')->willReturn(false);
        $this->assertEquals($this->model, $this->model->execute($observerMock));
    }

    public function testexecuteIfRegistryItemIdIsNull()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->dataMock->expects($this->once())->method('isEnabled')->willReturn(true);

        $productMock = $this->getMock('\Magento\Catalog\Model\Product', ['getGiftregistryItemId'], [], '', false);
        $productMock->expects($this->once())->method('getGiftregistryItemId')->willReturn(null);

        $quoteItemMock = $this->getMock('\Magento\Quote\Model\Quote\Item', [], [], '', false);

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getProduct', 'getQuoteItem'], [], '', false);
        $observerMock->expects($this->exactly(2))->method('getEvent')->willReturn($eventMock);

        $eventMock->expects($this->once())->method('getProduct')->willReturn($productMock);
        $eventMock->expects($this->once())->method('getQuoteItem')->willReturn($quoteItemMock);

        $this->assertEquals($this->model, $this->model->execute($observerMock));
    }

    public function testexecute()
    {
        $giftRegistryItemId = 100;
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->dataMock->expects($this->once())->method('isEnabled')->willReturn(true);

        $productMock = $this->getMock('\Magento\Catalog\Model\Product', ['getGiftregistryItemId'], [], '', false);
        $productMock->expects($this->once())->method('getGiftregistryItemId')->willReturn($giftRegistryItemId);

        $quoteItemMock = $this->getMock(
            '\Magento\Quote\Model\Quote\Item',
            ['setGiftregistryItemId', 'getParentItem'],
            [],
            '',
            false
        );

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getProduct', 'getQuoteItem'], [], '', false);
        $observerMock->expects($this->exactly(2))->method('getEvent')->willReturn($eventMock);

        $eventMock->expects($this->once())->method('getProduct')->willReturn($productMock);
        $eventMock->expects($this->once())->method('getQuoteItem')->willReturn($quoteItemMock);

        $quoteItemMock->expects($this->once())
            ->method('setGiftregistryItemId')
            ->with($giftRegistryItemId)
            ->willReturnSelf();

        $parentItemMock = $this->getMock('\Magento\Quote\Model\Quote\Item', ['setGiftregistryItemId'], [], '', false);
        $parentItemMock->expects($this->once())
            ->method('setGiftregistryItemId')
            ->with($giftRegistryItemId)
            ->willReturnSelf();

        $quoteItemMock->expects($this->once())->method('getParentItem')->willReturn($parentItemMock);

        $this->assertEquals($this->model, $this->model->execute($observerMock));
    }
}
