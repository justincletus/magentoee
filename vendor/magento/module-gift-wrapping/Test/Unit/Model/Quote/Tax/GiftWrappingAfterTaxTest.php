<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Test\Unit\Model\Quote\Tax;

use Magento\Quote\Model\Quote\Address;

/**
 * Test class for \Magento\GiftWrapping\Model\Quote\Tax\GiftwrappingAfterTax
 */
class GiftWrappingAfterTaxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\GiftWrapping\Model\Wrapping
     */
    protected $wrappingMock;

    protected function setUp()
    {
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
        $helperMock = $this->getMock('Magento\GiftWrapping\Helper\Data', [], [], '', false);
        $product = $this->getMock('Magento\Catalog\Model\Product', ['isVirtual', '__wakeup'], [], '', false);
        $storeMock = $this->getMock('Magento\Store\Model\Store', ['convertPrice', 'getId', '__wakeup'], [], '', false);

        $addressMock = $this->getMock(
            'Magento\Quote\Model\Quote\Address',
            [
                'getAddressType',
                '__wakeup',
                'getExtraTaxableDetails',
            ],
            [],
            '',
            false
        );

        $item = new \Magento\Framework\DataObject();
        $storeMock->expects($this->any())->method('convertPrice')->willReturn(10);
        $product->expects($this->any())->method('isVirtual')->willReturn(false);

        $quoteData = [
            'isMultishipping' => false,
            'store' => $storeMock,
            'billingAddress' => null,
            'customerTaxClassId' => null
        ];
        $quote = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false);
        $quote->setData($quoteData);

        $this->wrappingMock->expects($this->any())->method('load')->willReturnSelf();
        $this->wrappingMock->expects($this->any())->method('getBasePrice')->willReturn(6);

        $product->setGiftWrappingPrice(10);
        $item->setProduct($product)->setQty(2)->setGwId(1)->setGwPrice(5)->setGwBasePrice(10);
        $addressMock->expects($this->any())->method('getAddressType')->willReturn(Address::TYPE_SHIPPING);

        $shippingMock = $this->getMock('\Magento\Quote\Api\Data\ShippingInterface');
        $shippingMock->expects($this->atLeastOnce())->method('getAddress')->willReturn($addressMock);
        $shippingAssignmentMock = $this->getMock('\Magento\Quote\Api\Data\ShippingAssignmentInterface');
        $shippingAssignmentMock->expects($this->atLeastOnce())->method('getShipping')->willReturn($shippingMock);
        $totalMock = $this->getMock(
            '\Magento\Quote\Model\Quote\Address\Total',
            ['getExtraTaxableDetails', 'getGwItemCodeToItemMapping'],
            [],
            '',
            false
        );
        $itemGw = [
            'item_gw' =>
                [
                    'test_gw_item' => [
                        [
                            'code' => 'testCode',
                            'base_row_tax' => 100,
                            'row_tax' => 200,
                            'price_incl_tax' => 300,
                            'base_price_incl_tax' => 400
                        ]
                    ]
                ]
        ];
        $totalMock->expects($this->once())->method('getExtraTaxableDetails')->willReturn($itemGw);
        $totalMock->expects($this->once())->method('getGwItemCodeToItemMapping')->willReturn(['testCode' => $item]);

        $model = new \Magento\GiftWrapping\Model\Total\Quote\Tax\GiftwrappingAfterTax($helperMock);
        $model->collect($quote, $shippingAssignmentMock, $totalMock);
    }
}
