<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Test\Unit\Model\Quote;

use Magento\Quote\Model\Quote\Address;
use Magento\GiftWrapping\Model\Total\Quote\Giftwrapping;

/**
 * Test class for \Magento\GiftWrapping\Model\Quote\Giftwrapping
 */
class GiftWrappingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\GiftWrapping\Model\Wrapping
     */
    protected $wrappingMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Quote\Model\Quote\Address
     */
    protected $addressMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Test for collect method
     *
     * @param bool $withProduct
     * @dataProvider collectQuoteDataProvider
     */
    public function testCollectQuote($withProduct)
    {
        $shippingAssignmentMock = $this->_prepareData();
        $helperMock = $this->getMock('Magento\GiftWrapping\Helper\Data', [], [], '', false);
        $factoryMock = $this->getMock(
            'Magento\GiftWrapping\Model\WrappingFactory',
            ['create'],
            [],
            '',
            false
        );
        $factoryMock->expects($this->any())->method('create')->will($this->returnValue($this->wrappingMock));

        $model = new Giftwrapping($helperMock, $factoryMock, $this->priceCurrency);
        $item = new \Magento\Framework\DataObject();

        $product = $this->getMock('\Magento\Catalog\Model\Product', ['isVirtual', '__wakeup'], [], '', false);
        $product->expects($this->any())->method('isVirtual')->willReturn(false);
        if ($withProduct) {
            $product->setGiftWrappingPrice(10);
        } else {
            $product->setGiftWrappingPrice(0);
            $item->setWrapping($this->wrappingMock);
        }
        $item->setProduct($product)->setQty(2)->setGwId(1);

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $shippingAssignmentMock->expects($this->any())->method('getItems')->willReturn([$item]);

        $quoteMock = $this->getMock(
            '\Magento\Quote\Model\Quote',
            [
                'setGwItemsBasePrice', 'getStore', 'setGwItemsPrice', 'setGwBasePrice', 'setGwPrice',
                'setGwCardBasePrice', 'setGwCardPrice', 'getGwItemsBasePrice', 'getGwItemsPrice', 'getGwBasePrice',
                'getGwPrice', 'getGwCardBasePrice', 'getGwCardPrice'
            ],
            [],
            '',
            false
        );
        $quoteMock->expects($this->atLeastOnce())->method('setGwItemsBasePrice')->willReturnSelf();
        $quoteMock->expects($this->atLeastOnce())->method('setGwItemsPrice')->willReturnSelf();
        $quoteMock->expects($this->atLeastOnce())->method('setGwBasePrice')->willReturnSelf();
        $quoteMock->expects($this->atLeastOnce())->method('setGwPrice')->willReturnSelf();
        $quoteMock->expects($this->atLeastOnce())->method('setGwCardBasePrice')->willReturnSelf();
        $quoteMock->expects($this->atLeastOnce())->method('setGwCardPrice')->willReturnSelf();
        $quoteMock->expects($this->atLeastOnce())->method('getGwItemsBasePrice');
        $quoteMock->expects($this->atLeastOnce())->method('getGwItemsPrice');
        $quoteMock->expects($this->atLeastOnce())->method('getGwBasePrice');
        $quoteMock->expects($this->atLeastOnce())->method('getGwPrice');
        $quoteMock->expects($this->atLeastOnce())->method('getGwCardBasePrice');
        $quoteMock->expects($this->atLeastOnce())->method('getGwCardPrice');
        $quoteMock->expects($this->once())->method('getStore')->willReturn($storeMock);

        $totalMock = $this->getMock(
            '\Magento\Quote\Model\Quote\Address\Total',
            [
                'setBaseGrandTotal', 'getBaseGrandTotal', 'getGwItemsBasePrice', 'getGwBasePrice', 'getGwCardBasePrice',
                'getGrandTotal', 'getGwItemsPrice', 'getGwPrice', 'getGwCardPrice',
                'setGwItemsBasePrice', 'setGwItemsPrice', 'setGwItemIds', 'setGwCardBasePrice', 'setGwCardPrice',
                'setGwAddCard'
            ],
            [],
            '',
            false
        );
        $totalMock->expects($this->atLeastOnce())->method('setBaseGrandTotal');
        $totalMock->expects($this->atLeastOnce())->method('getBaseGrandTotal');
        $totalMock->expects($this->atLeastOnce())->method('getGwItemsBasePrice');
        $totalMock->expects($this->atLeastOnce())->method('getGwBasePrice');
        $totalMock->expects($this->atLeastOnce())->method('getGwCardBasePrice');
        $totalMock->expects($this->atLeastOnce())->method('getGrandTotal');
        $totalMock->expects($this->atLeastOnce())->method('getGwItemsPrice');
        $totalMock->expects($this->atLeastOnce())->method('getGwPrice');
        $totalMock->expects($this->atLeastOnce())->method('getGwCardPrice');
        $totalMock->expects($this->atLeastOnce())->method('setGwItemsBasePrice');
        $totalMock->expects($this->atLeastOnce())->method('setGwItemsPrice');
        $totalMock->expects($this->atLeastOnce())->method('setGwItemIds');
        $totalMock->expects($this->atLeastOnce())->method('setGwCardBasePrice');
        $totalMock->expects($this->atLeastOnce())->method('setGwCardPrice');
        $totalMock->expects($this->atLeastOnce())->method('setGwAddCard');

        $model->collect($quoteMock, $shippingAssignmentMock, $totalMock);
    }

    /**
     * Prepare mocks for test
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _prepareData()
    {

        $this->wrappingMock = $this->getMock(
            '\Magento\GiftWrapping\Model\Wrapping',
            ['load', 'setStoreId', 'getBasePrice', '__wakeup'],
            [],
            '',
            false
        );
        $this->addressMock = $this->getMock(
            'Magento\Quote\Model\Quote\Address',
            [
                'getAddressType',
                'getQuote',
                'getAllItems',
                '__wakeup'
            ],
            [],
            '',
            false
        );

        $this->priceCurrency = $this->getMockBuilder('Magento\Framework\Pricing\PriceCurrencyInterface')->getMock();
        $this->priceCurrency->expects($this->any())->method('convert')->will($this->returnValue(10));

        $this->wrappingMock->expects($this->any())->method('load')->will($this->returnSelf());
        $this->wrappingMock->expects($this->any())->method('getBasePrice')->will($this->returnValue(6));
        $this->addressMock->expects($this->any())->method('getAddressType')->willReturn(Address::TYPE_SHIPPING);

        $shippingAssignmentMock = $this->getMock('\Magento\Quote\Api\Data\ShippingAssignmentInterface');

        $shippingMock = $this->getMock('\Magento\Quote\Api\Data\ShippingInterface');
        $shippingAssignmentMock->expects($this->once())->method('getShipping')->willReturn($shippingMock);
        $shippingMock->expects($this->once())->method('getAddress')->willReturn($this->addressMock);

        return $shippingAssignmentMock;
    }

    /**
     * Data provider for testCollectQuote
     *
     * @return array
     */
    public function collectQuoteDataProvider()
    {
        return [
            'withProduct' => [true],
            'withoutProduct' => [false]
        ];
    }
}
