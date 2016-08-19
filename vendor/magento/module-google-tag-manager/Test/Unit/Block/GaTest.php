<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GoogleTagManager\Test\Unit\Block;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class GaTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GoogleTagManager\Block\Ga */
    protected $ga;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $collectionFactory;

    /** @var \Magento\GoogleTagManager\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $googleTagManagerHelper;

    /** @var \Magento\Cookie\Helper\Cookie|\PHPUnit_Framework_MockObject_MockObject */
    protected $cookieHelper;

    /** @var \Magento\Framework\Json\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $jsonHelper;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    protected function setUp()
    {
        $this->collectionFactory = $this->getMock(
            'Magento\Sales\Model\ResourceModel\Order\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->googleTagManagerHelper = $this->getMock('Magento\GoogleTagManager\Helper\Data', [], [], '', false);
        $this->cookieHelper = $this->getMock('Magento\Cookie\Helper\Cookie', [], [], '', false);
        $this->jsonHelper = $this->getMock('Magento\Framework\Json\Helper\Data', [], [], '', false);
        $this->storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface', [], [], '', false);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->ga = $this->objectManagerHelper->getObject(
            'Magento\GoogleTagManager\Block\Ga',
            [
                'salesOrderCollection' => $this->collectionFactory,
                'googleAnalyticsData' => $this->googleTagManagerHelper,
                'cookieHelper' => $this->cookieHelper,
                'jsonHelper' => $this->jsonHelper,
                'storeManager' => $this->storeManager
            ]
        );
    }

    public function testToHtml()
    {
        $this->googleTagManagerHelper->expects($this->atLeastOnce())->method('isGoogleAnalyticsAvailable')
            ->willReturn(true);
        $this->ga->toHtml();
    }

    public function testGetStoreCurrencyCode()
    {
        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $store->expects($this->atLeastOnce())->method('getBaseCurrencyCode')->willReturn('USD');
        $this->storeManager->expects($this->atLeastOnce())->method('getStore')->with(null)->willReturn($store);
        $this->assertEquals('USD', $this->ga->getStoreCurrencyCode());
    }

    public function testGetOrdersDataEmptyOrderIds()
    {
        $this->assertEmpty($this->ga->getOrdersData());
    }

    public function testGetOrdersData()
    {
        $this->ga->setOrderIds([12, 13]);

        $item1 = $this->getMock('Magento\Sales\Model\Order\Item', [], [], '', false);
        $item1->expects($this->atLeastOnce())->method('getSku')->willReturn('SKU-123');
        $item1->expects($this->atLeastOnce())->method('getName')->willReturn('Product Name');
        $item1->expects($this->atLeastOnce())->method('getBasePrice')->willReturn(85);
        $item1->expects($this->atLeastOnce())->method('getQtyOrdered')->willReturn(1);

        $item2 = $this->getMock('Magento\Sales\Model\Order\Item', [], [], '', false);
        $item2->expects($this->atLeastOnce())->method('getSku')->willReturn('SKU-123');
        $item2->expects($this->atLeastOnce())->method('getName')->willReturn('Product Name');
        $item2->expects($this->atLeastOnce())->method('getBasePrice')->willReturn(85);
        $item2->expects($this->atLeastOnce())->method('getQtyOrdered')->willReturn(1);

        $order = $this->getMock('Magento\Sales\Model\Order', [], [], '', false);
        $order->expects($this->atLeastOnce())->method('getIncrementId')->willReturn('10002323');
        $order->expects($this->atLeastOnce())->method('getBaseGrandTotal')->willReturn(120);
        $order->expects($this->atLeastOnce())->method('getBaseTaxAmount')->willReturn(15);
        $order->expects($this->atLeastOnce())->method('getBaseShippingAmount')->willReturn(20);
        $order->expects($this->atLeastOnce())->method('getCouponCode')->willReturn('ABC123123');
        $order->expects($this->atLeastOnce())->method('getAllVisibleItems')->willReturn([$item1, $item2]);

        $collection = $this->getMock('Magento\Sales\Model\ResourceModel\Order\Collection', [], [], '', false);
        $collection->expects($this->once())->method('addFieldToFilter')->with('entity_id', ['in' => [12, 13]]);
        $collection->expects($this->once())->method('getIterator')->willReturn(
            new \ArrayIterator([$order])
        );

        $this->collectionFactory->expects($this->once())->method('create')->willReturn($collection);

        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $store->expects($this->atLeastOnce())->method('getBaseCurrencyCode')->willReturn('USD');
        $this->storeManager->expects($this->atLeastOnce())->method('getStore')->with(null)->willReturn($store);

        $json = [
            'ecommerce' => [
                'purchase' => [
                    'actionField' => [
                        'id' => '10002323',
                        'revenue' => 85,
                        'tax' => 15,
                        'shipping' => 20,
                        'coupon' => 'ABC123123'
                    ],
                    'products' => [
                        0 => [
                            'id' => 'SKU-123',
                            'name' => 'Product Name',
                            'price' => 85,
                            'quantity' => 1
                        ],
                        1 => [
                            'id' => 'SKU-123',
                            'name' => 'Product Name',
                            'price' => 85,
                            'quantity' => 1
                        ],
                    ],
                ],
                'currencyCode' => 'USD'
            ],
            'event' => 'purchase'
        ];

        $this->jsonHelper->expects($this->once())->method('jsonEncode')->with($json)->willReturn('{encoded_string}');
        $this->assertEquals("dataLayer.push({encoded_string});\n", $this->ga->getOrdersData());
    }

    public function testIsUserNotAllowSaveCookie()
    {
        $this->cookieHelper->expects($this->atLeastOnce())->method('isUserNotAllowSaveCookie')->willReturn(true);
        $this->assertTrue($this->ga->isUserNotAllowSaveCookie());
    }
}
