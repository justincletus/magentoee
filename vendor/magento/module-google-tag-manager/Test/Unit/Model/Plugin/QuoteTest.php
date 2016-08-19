<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GoogleTagManager\Test\Unit\Model\Plugin;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class QuoteTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GoogleTagManager\Model\Plugin\Quote */
    protected $quote;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\GoogleTagManager\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $helper;

    /** @var \Magento\Checkout\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $session;

    /** @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject */
    protected $registry;

    protected function setUp()
    {
        $this->helper = $this->getMock('Magento\GoogleTagManager\Helper\Data', [], [], '', false);
        $this->session = $this->getMock('Magento\Checkout\Model\Session', ['hasData', 'setData'], [], '', false);
        $this->registry = $this->getMock('Magento\Framework\Registry');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->quote = $this->objectManagerHelper->getObject(
            'Magento\GoogleTagManager\Model\Plugin\Quote',
            [
                'helper' => $this->helper,
                'checkoutSession' => $this->session,
                'registry' => $this->registry
            ]
        );
    }

    /**
     * @param string $type
     * @param int $qty
     * @param bool $available
     * @param mixed $setDataCall
     * @param [] $expected
     *
     * @dataProvider afterLoadDataProvider
     */
    public function testAfterLoad($type, $qty, $available, $setDataCall, $expected)
    {
        $option = $this->getMock('Magento\Quote\Model\Quote\Item\Option', ['getProductId'], [], '', false);
        $option->expects($this->any())->method('getProductId')->willReturn('GroupedId');

        $parentItem = $this->getMock('Magento\Quote\Model\Quote\Item', ['getProductId', 'getQty'], [], '', false);
        $parentItem->expects($this->any())->method('getQty')->willReturn(10);
        $parentItem->expects($this->any())->method('getProductId')->willReturn('ParentId');

        $item = $this->getMock(
            'Magento\Quote\Model\Quote\Item',
            [
                'getProductType',
                'getOptionByCode',
                'getProductId',
                'getId',
                'getQty',
                'getParentItem',
            ],
            [],
            '',
            false
        );
        $item->expects($this->any())->method('getProductType')->willReturn($type);
        $item->expects($this->any())->method('getOptionByCode')->with('product_type')->willReturn($option);
        $item->expects($this->any())->method('getProductId')->willReturn('ProductId');
        $item->expects($this->any())->method('getId')->willReturn('Id');
        $item->expects($this->any())->method('getQty')->willReturn($qty);
        $item->expects($this->any())->method('getParentItem')->willReturn($parentItem);

        $items = [$item];

        $subject = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false);
        $subject->expects($this->any())->method('getAllItems')->willReturn($items);

        $this->helper->expects($this->atLeastOnce())->method('isTagManagerAvailable')->willReturn($available);

        $this->session->expects($this->any())->method('hasData')
            ->with(\Magento\GoogleTagManager\Helper\Data::PRODUCT_QUANTITIES_BEFORE_ADDTOCART)
            ->willReturn(false);
        $this->session->expects($setDataCall)->method('setData')
            ->with(
                \Magento\GoogleTagManager\Helper\Data::PRODUCT_QUANTITIES_BEFORE_ADDTOCART,
                $this->equalTo($expected)
            );
        $this->assertSame($subject, $this->quote->afterLoad($subject, $subject));
    }

    public function afterLoadDataProvider()
    {
        return [
            ['bundle', 1, true, $this->once(), []],
            ['configurable', 2, true, $this->once(), []],
            ['grouped', 3, true, $this->once(), ['GroupedId-ProductId' => 3]],
            ['giftcard', 4, true, $this->once(), ['Id-ProductId' => 4]],
            ['simple', 5, true, $this->once(), ['Id-ParentId-ProductId' => 50]],
            ['', 0, false, $this->never(), []],
        ];
    }

    public function testAfterLoadForProductWithoutParent()
    {
        $option = $this->getMock('Magento\Quote\Model\Quote\Item\Option', ['getProductId'], [], '', false);
        $option->expects($this->any())->method('getProductId')->willReturn('GroupedId');

        $item = $this->getMock(
            'Magento\Quote\Model\Quote\Item',
            [
                'getProductType',
                'getOptionByCode',
                'getProductId',
                'getId',
                'getQty',
                'getParentItem',
            ],
            [],
            '',
            false
        );
        $item->expects($this->any())->method('getProductType')->willReturn('simple');
        $item->expects($this->any())->method('getProductId')->willReturn('ProductId');
        $item->expects($this->any())->method('getQty')->willReturn(17);
        $item->expects($this->any())->method('getParentItem')->willReturn(null);

        $subject = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false);
        $subject->expects($this->any())->method('getAllItems')->willReturn([$item]);

        $this->helper->expects($this->atLeastOnce())->method('isTagManagerAvailable')->willReturn(true);

        $this->session->expects($this->any())->method('hasData')
            ->with(\Magento\GoogleTagManager\Helper\Data::PRODUCT_QUANTITIES_BEFORE_ADDTOCART)
            ->willReturn(false);
        $this->session->expects($this->once())->method('setData')
            ->with(
                \Magento\GoogleTagManager\Helper\Data::PRODUCT_QUANTITIES_BEFORE_ADDTOCART,
                $this->equalTo(['ProductId' => 17])
            );
        $this->assertSame($subject, $this->quote->afterLoad($subject, $subject));
    }
}
