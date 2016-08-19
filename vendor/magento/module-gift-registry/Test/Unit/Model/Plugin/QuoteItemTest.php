<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Test\Unit\Model\Plugin;

class QuoteItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Model\Plugin\QuoteItem
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteItemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderItemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \Closure
     */
    protected $closureMock;

    protected function setUp()
    {
        $this->orderItemMock = $this->getMock(
            'Magento\Sales\Model\Order\Item',
            ['setGiftregistryItemId', '__wakeup'],
            [],
            '',
            false
        );
        $this->quoteItemMock = $this->getMock('Magento\Quote\Model\Quote\Item', [], [], '', false);
        $this->subjectMock = $this->getMock('Magento\Quote\Model\Quote\Item\ToOrderItem', [], [], '', false);
        $orderItems = $this->orderItemMock;
        $this->closureMock = function () use ($orderItems) {
            return $orderItems;
        };
        $this->model = new \Magento\GiftRegistry\Model\Plugin\QuoteItem();
    }

    /**
     * @dataProvider registryIdProvider
     * @param $registryId
     */
    public function testAroundItemToOrderUsualQuote($registryId)
    {
        $quoteItemMock = $this->getMock(
            'Magento\Quote\Model\Quote\Item',
            ['getQuoteItem', 'getGiftregistryItemId', '__wakeup'],
            [],
            '',
            false
        );
        $quoteItemMock->expects($this->once())->method('getGiftregistryItemId')->will($this->returnValue($registryId));

        if ($registryId) {
            $this->orderItemMock->expects($this->once())->method('setGiftregistryItemId')->with($registryId);
        }
        $orderItem = $this->model->aroundConvert($this->subjectMock, $this->closureMock, $quoteItemMock, []);
        $this->assertSame($this->orderItemMock, $orderItem);
    }

    /**
     * @dataProvider registryIdProvider
     * @param $registryId
     */
    public function testAroundItemToOrderQuoteAddress($registryId)
    {
        $quoteItemMock = $this->getMock(
            'Magento\Quote\Model\Quote\Address\Item',
            ['getQuoteItem', 'getGiftregistryItemId', '__wakeup'],
            [],
            '',
            false
        );
        $stdMock = $this->getMock('stdClass', ['getGiftregistryItemId'], [], '', false);
        $quoteItemMock->expects($this->once())->method('getQuoteItem')->will($this->returnValue($stdMock));

        $stdMock->expects($this->once())->method('getGiftregistryItemId')->will($this->returnValue($registryId));

        if ($registryId) {
            $this->orderItemMock->expects($this->once())->method('setGiftregistryItemId')->with($registryId);
        }
        $orderItem = $this->model->aroundConvert($this->subjectMock, $this->closureMock, $quoteItemMock, []);
        $this->assertSame($this->orderItemMock, $orderItem);
    }

    public function registryIdProvider()
    {
        return [[false], [2]];
    }
}
