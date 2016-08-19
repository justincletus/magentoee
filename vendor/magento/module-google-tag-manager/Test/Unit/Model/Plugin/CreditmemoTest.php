<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GoogleTagManager\Test\Unit\Model\Plugin;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class CreditmemoTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GoogleTagManager\Model\Plugin\Creditmemo */
    protected $creditmemo;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\GoogleTagManager\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $helper;

    /** @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $session;

    protected function setUp()
    {
        $this->helper = $this->getMock('Magento\GoogleTagManager\Helper\Data', [], [], '', false);
        $this->session = $this->getMock('Magento\Backend\Model\Session', ['setData'], [], '', false);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->creditmemo = $this->objectManagerHelper->getObject(
            'Magento\GoogleTagManager\Model\Plugin\Creditmemo',
            [
                'helper' => $this->helper,
                'backendSession' => $this->session
            ]
        );
    }

    public function testAfterSave()
    {
        $this->helper->expects($this->atLeastOnce())->method('isTagManagerAvailable')->willReturn(true);

        $this->session->expects($this->any())
            ->method('setData')
            ->withConsecutive(
                [
                    'googleanalytics_creditmemo_order',
                    '00000001'
                ],
                [
                    'googleanalytics_creditmemo_store_id',
                    2
                ],
                [
                    'googleanalytics_creditmemo_revenue',
                    '19.99'
                ],
                [
                    'googleanalytics_creditmemo_products',
                    [
                        [
                            'id' => 'Item 1',
                            'quantity' => 3
                        ]
                    ]
                ]
            )
            ->willReturnSelf();

        $order = $this->getMock('Magento\Sales\Model\Order', [], [], '', false);
        $order->expects($this->any())->method('getIncrementId')->willReturn('00000001');
        $order->expects($this->any())->method('getBaseGrandTotal')->willReturn('29.99');

        $item1 = $this->getMock('Magento\Sales\Model\Order\Creditmemo\Item', [], [], '', false);
        $item1->expects($this->any())->method('getQty')->willReturn(3);
        $item1->expects($this->any())->method('getSku')->willReturn('Item 1');

        $item2 = $this->getMock('Magento\Sales\Model\Order\Creditmemo\Item', [], [], '', false);
        $item2->expects($this->any())->method('getQty')->willReturn(0);
        $item2->expects($this->any())->method('getSku')->willReturn('Item 2');

        $collection = $this->getMock(
            'Magento\Sales\Model\ResourceModel\Order\Creditmemo\Item\Collection',
            [],
            [],
            '',
            false
        );
        $collection->expects($this->any())->method('getIterator')
            ->willReturn(new \ArrayIterator([$item1, $item2]));

        /** @var \Magento\Sales\Model\Order\Creditmemo|\PHPUnit_Framework_MockObject_MockObject $result */
        $result = $this->getMock('Magento\Sales\Model\Order\Creditmemo', [], [], '', false);
        $result->expects($this->any())->method('getOrder')->willReturn($order);
        $result->expects($this->any())->method('getStoreId')->willReturn(2);
        $result->expects($this->any())->method('getBaseGrandTotal')->willReturn('19.99');
        $result->expects($this->any())->method('getItemsCollection')->willReturn($collection);

        $this->assertSame($result, $this->creditmemo->afterSave($result, $result));
    }

    public function testAfterSaveNotAvailable()
    {
        $this->helper->expects($this->atLeastOnce())->method('isTagManagerAvailable')->willReturn(false);
        /** @var \Magento\Sales\Model\Order\Creditmemo|\PHPUnit_Framework_MockObject_MockObject $result */
        $result = $this->getMock('Magento\Sales\Model\Order\Creditmemo', [], [], '', false);
        $result->expects($this->never())->method('getOrder');
        $this->session->expects($this->never())->method('setData');

        $this->assertSame($result, $this->creditmemo->afterSave($result, $result));
    }
}
