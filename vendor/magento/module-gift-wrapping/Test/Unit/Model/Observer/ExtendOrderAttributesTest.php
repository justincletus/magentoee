<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Test\Unit\Model\Observer;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Unit test of order attributes extension observer.
 */
class ExtendOrderAttributesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Subject of testing.
     *
     * @var \Magento\GiftWrapping\Model\Observer\ExtendOrderAttributes
     */
    protected $subject;

    /**
     * Event observer mock.
     *
     * @var \Magento\Framework\Event\Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    /**
     * Event mock.
     *
     * @var \Magento\Framework\DataObject|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventMock;

    /**
     * Order model mock.
     *
     * @var \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderMock;

    /**
     * Quote address model mock.
     *
     * @var \Magento\Quote\Model\Address|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteAddressMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->observerMock = $this->getMock(
            'Magento\Framework\Event\Observer',
            ['getEvent'],
            [],
            '',
            false
        );

        $this->eventMock = $this->getMock(
            '\Magento\Framework\DataObject',
            ['getOrder', 'getQuote'],
            [],
            '',
            false
        );

        $this->observerMock->expects($this->any())
            ->method('getEvent')
            ->willReturn($this->eventMock);

        $this->orderMock = $this->getMock(
            'Magento\Sales\Model\Order',
            null,
            [],
            '',
            false
        );

        $quoteMock = $this->getMock(
            'Magento\Quote\Model\Quote',
            ['getShippingAddress'],
            [],
            '',
            false
        );

        $this->quoteAddressMock = $this->getMock('Magento\Quote\Model\Quote\Address', null, [], '', false);
        $quoteMock->expects($this->once())->method('getShippingAddress')->willReturn($this->quoteAddressMock);

        $this->eventMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($this->orderMock);

        $this->eventMock->expects($this->any())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $this->subject = $objectManager->getObject(
            'Magento\GiftWrapping\Model\Observer\ExtendOrderAttributes'
        );
    }

    public function testExecute()
    {
        $gwBasePriceInclTax = 25;

        $this->quoteAddressMock->setData('gw_id', 1);
        $this->quoteAddressMock->setData('gw_allow_gift_receipt', true);
        $this->quoteAddressMock->setData('gw_base_price_incl_tax', $gwBasePriceInclTax);
        $this->quoteAddressMock->setData('wrong_atribute', true);

        $this->subject->execute($this->observerMock);

        $this->assertTrue(
            $this->orderMock->hasData('gw_id'),
            'An attribute should be present in the Order!'
        );

        $this->assertTrue(
            $this->orderMock->hasData('gw_allow_gift_receipt'),
            'An attribute should be present in the Order!'
        );

        $this->assertEquals($this->orderMock->getGwBasePriceInclTax(), $gwBasePriceInclTax);

        $this->assertFalse(
            $this->orderMock->hasData('wrong_atribute'),
            'An attribute should NOT be present in the Order!'
        );
    }
}
