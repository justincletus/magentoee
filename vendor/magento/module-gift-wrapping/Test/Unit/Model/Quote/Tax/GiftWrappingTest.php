<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Test\Unit\Model\Quote\Tax;

use Magento\Quote\Model\Quote\Address;

/**
 * Test class for \Magento\GiftWrapping\Model\Quote\Tax\Giftwrapping
 */
class GiftWrappingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\GiftWrapping\Model\Wrapping
     */
    protected $wrappingMock;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->wrappingMock = $this->getMock(
            'Magento\GiftWrapping\Model\Wrapping',
            ['load', 'setStoreId', 'getBasePrice', '__wakeup'],
            [],
            '',
            false
        );
    }

    /**
     * Test for collect method
     */
    public function testCollectQuote()
    {
        $helperMock = $this->getMock('\Magento\GiftWrapping\Helper\Data', [], [], '', false);
        $helperMock->expects($this->any())->method('getWrappingTaxClass')->will($this->returnValue(2));
        $wrappingFactoryMock = $this->getMock('\Magento\GiftWrapping\Model\WrappingFactory', ['create'], [], '', false);
        $wrappingFactoryMock->expects($this->once())->method('create')->willReturn($this->wrappingMock);
        $product = $this->getMock(
            '\Magento\Catalog\Model\Product',
            ['setGiftWrappingPrice', 'isVirtual', '__wakeup'],
            [],
            '',
            false
        );
        $addressMock = $this->getMock(
            'Magento\Quote\Model\Quote\Address',
            [
                'getAddressType',
                '__wakeup',
                'setGwItemsBaseTaxAmount',
                'setGwItemsTaxAmount',
                'getExtraTaxableDetails',
                'getCustomAttributesCodes'
            ],
            [],
            '',
            false
        );
        $product->expects($this->any())->method('isVirtual')->willReturn(false);
        $this->wrappingMock->expects($this->any())->method('load')->willReturnSelf();
        $this->wrappingMock->expects($this->any())->method('getBasePrice')->willReturn(6);

        $item = $this->getMock(
            '\Magento\Quote\Model\Quote\Item',
            [
                'setAssociatedTaxables',
                '__wakeup',
                'setProduct',
                'getProduct',
                'getQty',
                'getGwId'
            ],
            [],
            '',
            false
        );
        $product->setGiftWrappingPrice(10);
        $item->expects($this->once())->method('getGwId')->willReturn(1);
        $item->expects($this->any())->method('getProduct')->willReturn($product);
        $item->expects($this->any())->method('getQty')->willReturn(2);
        $addressMock->expects($this->any())->method('getAddressType')->willReturn('shipping');
        $addressMock->expects($this->any())->method('getCustomAttributesCodes')->willReturn([]);

        $expected = [
            [
                'type' => 'item_gw',
                'code' => 'item_gw1',
                'unit_price' => 10,
                'base_unit_price' => 6,
                'quantity' => 2,
                'tax_class_id' => 2,
                'price_includes_tax' => false,
            ],
        ];
        $item->expects($this->once())->method('setAssociatedTaxables')->with($expected);
        $storeMock = $this->getMock('\Magento\Store\Model\Store', ['convertPrice', 'getId', '__wakeup'], [], '', false);
        $storeMock->expects($this->any())->method('convertPrice')->willReturn(10);

        $quoteData = [
            'isMultishipping' => false,
            'store' => $storeMock,
            'billingAddress' => null,
            'customerTaxClassId' => null,
            'tax_class_id' => 2,
        ];
        $quote = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false);
        $quote->expects($this->any())->method('setData')->with($quoteData)->willReturnSelf();
        $quote->expects($this->once())->method('getStore')->willReturn($storeMock);
        $this->wrappingMock->expects($this->once())->method('setStoreId')->willReturnSelf();
        $this->wrappingMock->expects($this->once())->method('load')->willReturnSelf();
        $priceCurrencyMock = $this->getMock('Magento\Framework\Pricing\PriceCurrencyInterface');
        $helperMock->expects($this->any())->method('getPrintedCardPrice');
        $priceCurrencyMock->expects($this->any())->method('convert')->willReturn(10);
        $shippingMock = $this->getMock('\Magento\Quote\Api\Data\ShippingInterface');
        $shippingMock->expects($this->atLeastOnce())->method('getAddress')->willReturn($addressMock);
        $shippingAssignmentMock = $this->getMock('\Magento\Quote\Api\Data\ShippingAssignmentInterface');
        $shippingAssignmentMock->expects($this->atLeastOnce())->method('getShipping')->willReturn($shippingMock);
        $shippingAssignmentMock->expects($this->once())->method('getItems')->willReturn([$item]);
        $totalMock = $this->getMock('\Magento\Quote\Model\Quote\Address\Total', [], [], '', false);
        $model = new \Magento\GiftWrapping\Model\Total\Quote\Tax\Giftwrapping(
            $helperMock,
            $priceCurrencyMock,
            $wrappingFactoryMock
        );
        $model->collect($quote, $shippingAssignmentMock, $totalMock);
    }
}
