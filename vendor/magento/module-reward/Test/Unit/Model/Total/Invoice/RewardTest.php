<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Model\Total\Invoice;

class RewardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Total\Creditmemo\Reward
     */
    protected $model;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Reward\Model\Total\Invoice\Reward');
    }

    /**
     * baseRewardCurrecnyAmountLeft == 0
     */
    public function testCollectIfBaseRewardCurrencyAmountLeftIsZero()
    {
        $invoiceMock = $this->getMock(
            '\Magento\Sales\Model\Order\Invoice',
            ['getOrder', 'getBaseGrandTotal'],
            [],
            '',
            false
        );

        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            [
                'getRewardCurrencyAmount',
                'getRwrdCurrencyAmountInvoiced',
                'getBaseRewardCurrencyAmount',
                'getBaseRwrdCrrncyAmtInvoiced'
            ],
            [],
            '',
            false
        );
        $invoiceMock->expects($this->once())->method('getOrder')->willReturn($orderMock);
        $invoiceMock->expects($this->never())->method('getBaseGrandTotal');

        $orderMock->expects($this->once())->method('getRewardCurrencyAmount')->willReturn(1);
        $orderMock->expects($this->once())->method('getRwrdCurrencyAmountInvoiced')->willReturn(1);
        $orderMock->expects($this->exactly(2))->method('getBaseRewardCurrencyAmount')->willReturn(1);
        $orderMock->expects($this->once())->method('getBaseRwrdCrrncyAmtInvoiced')->willReturn(1);

        $this->assertEquals($this->model, $this->model->collect($invoiceMock));
    }

    /**
     *  baseRewardCurrencyAmount == 0
     */
    public function testCollectIfBaseRewardCurrencyAmountIsZero()
    {
        $invoiceMock = $this->getMock(
            '\Magento\Sales\Model\Order\Invoice',
            ['getOrder', 'getBaseGrandTotal'],
            [],
            '',
            false
        );

        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            [
                'getRewardCurrencyAmount',
                'getRwrdCurrencyAmountInvoiced',
                'getBaseRewardCurrencyAmount',
                'getBaseRwrdCrrncyAmtInvoiced'
            ],
            [],
            '',
            false
        );
        $invoiceMock->expects($this->once())->method('getOrder')->willReturn($orderMock);
        $invoiceMock->expects($this->never())->method('getBaseGrandTotal');

        $orderMock->expects($this->once())->method('getRewardCurrencyAmount')->willReturn(1);
        $orderMock->expects($this->once())->method('getRwrdCurrencyAmountInvoiced')->willReturn(1);
        $orderMock->expects($this->exactly(2))->method('getBaseRewardCurrencyAmount')->willReturn(0);
        $orderMock->expects($this->once())->method('getBaseRwrdCrrncyAmtInvoiced')->willReturn(1);

        $this->assertEquals($this->model, $this->model->collect($invoiceMock));
    }

    /**
     *  baseRewardCurrecnyAmountLeft > baseGrandTotal
     */
    public function testCollectIfBaseRewardCurrencyAmountLeftGreaterThanZero()
    {
        $invoiceMock = $this->getMock(
            '\Magento\Sales\Model\Order\Invoice',
            [
                'getOrder',
                'getBaseGrandTotal',
                'getGrandTotal',
                'setGrandTotal',
                'setBaseGrandTotal',
                'setRewardPointsBalance',
                'setRewardCurrencyAmount',
                'setBaseRewardCurrencyAmount'
            ],
            [],
            '',
            false
        );
        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            [
                'getRewardCurrencyAmount',
                'getRwrdCurrencyAmountInvoiced',
                'getBaseRewardCurrencyAmount',
                'getBaseRwrdCrrncyAmtInvoiced',
                'getRewardPointsBalance'
            ],
            [],
            '',
            false
        );
        $invoiceMock->expects($this->once())->method('getOrder')->willReturn($orderMock);
        $invoiceMock->expects($this->exactly(2))->method('getBaseGrandTotal')->willReturn(1);
        $invoiceMock->expects($this->once())->method('getGrandTotal')->willReturn(10);

        $invoiceMock->expects($this->once())->method('setGrandTotal')->with(0)->willReturnSelf();
        $invoiceMock->expects($this->once())->method('setBaseGrandTotal')->with(0)->willReturnSelf();

        $invoiceMock->expects($this->once())->method('setRewardPointsBalance')->with(3)->willReturnSelf();
        $invoiceMock->expects($this->once())->method('setRewardCurrencyAmount')->with(10)->willReturnSelf();
        $invoiceMock->expects($this->once())->method('setBaseRewardCurrencyAmount')->with(1)->willReturnSelf();

        $orderMock->expects($this->once())->method('getRewardCurrencyAmount')->willReturn(1);
        $orderMock->expects($this->once())->method('getRwrdCurrencyAmountInvoiced')->willReturn(1);
        $orderMock->expects($this->exactly(3))->method('getBaseRewardCurrencyAmount')->willReturn(20);
        $orderMock->expects($this->once())->method('getBaseRwrdCrrncyAmtInvoiced')->willReturn(1);
        $orderMock->expects($this->exactly(2))->method('getRewardPointsBalance')->willReturn(50);

        $this->assertEquals($this->model, $this->model->collect($invoiceMock));
    }

    /**
     *  baseRewardCurrecnyAmountLeft < baseGrandTotal
     */
    public function testCollectIfBaseRewardCurrencyAmountLeftLessThanZero()
    {
        $invoiceMock = $this->getMock(
            '\Magento\Sales\Model\Order\Invoice',
            [
                'getOrder',
                'getBaseGrandTotal',
                'getGrandTotal',
                'setGrandTotal',
                'setBaseGrandTotal',
                'setRewardPointsBalance',
                'setRewardCurrencyAmount',
                'setBaseRewardCurrencyAmount'
            ],
            [],
            '',
            false
        );
        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            [
                'getRewardCurrencyAmount',
                'getRwrdCurrencyAmountInvoiced',
                'getBaseRewardCurrencyAmount',
                'getBaseRwrdCrrncyAmtInvoiced',
                'getRewardPointsBalance'
            ],
            [],
            '',
            false
        );
        $invoiceMock->expects($this->once())->method('getOrder')->willReturn($orderMock);
        $invoiceMock->expects($this->exactly(2))->method('getBaseGrandTotal')->willReturn(3);
        $invoiceMock->expects($this->once())->method('getGrandTotal')->willReturn(10);

        $invoiceMock->expects($this->once())->method('setGrandTotal')->with(10)->willReturnSelf();
        $invoiceMock->expects($this->once())->method('setBaseGrandTotal')->with(1)->willReturnSelf();

        $invoiceMock->expects($this->once())->method('setRewardPointsBalance')->with(6)->willReturnSelf();
        $invoiceMock->expects($this->once())->method('setRewardCurrencyAmount')->with(0)->willReturnSelf();
        $invoiceMock->expects($this->once())->method('setBaseRewardCurrencyAmount')->with(2)->willReturnSelf();

        $orderMock->expects($this->any())->method('getRewardCurrencyAmount')->willReturn(1);
        $orderMock->expects($this->any())->method('getRwrdCurrencyAmountInvoiced')->willReturn(1);
        $orderMock->expects($this->exactly(3))->method('getBaseRewardCurrencyAmount')->willReturn(20);
        $orderMock->expects($this->any())->method('getBaseRwrdCrrncyAmtInvoiced')->willReturn(18);
        $orderMock->expects($this->exactly(2))->method('getRewardPointsBalance')->willReturn(50);

        $this->assertEquals($this->model, $this->model->collect($invoiceMock));
    }
}
