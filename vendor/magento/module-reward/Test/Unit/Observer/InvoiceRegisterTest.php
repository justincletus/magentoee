<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Observer;

class InvoiceRegisterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Observer\InvitationToCustomer
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->subject = $objectManager->getObject('Magento\Reward\Observer\InvoiceRegister');
    }

    public function testAddRewardsIfRewardCurrencyAmountIsNull()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $invoiceMock = $this->getMock(
            '\Magento\Sales\Model\Order\Invoice',
            ['getBaseRewardCurrencyAmount', '__wakeup'],
            [],
            '',
            false
        );
        $invoiceMock->expects($this->once())->method('getBaseRewardCurrencyAmount')->will($this->returnValue(null));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getInvoice'], [], '', false);
        $eventMock->expects($this->once())->method('getInvoice')->will($this->returnValue($invoiceMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testAddRewardsSuccess()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $invoiceMock = $this->getMock(
            '\Magento\Sales\Model\Order\Invoice',
            [
                'getBaseRewardCurrencyAmount',
                '__wakeup',
                'getOrder',
                'getRewardCurrencyAmount'
            ],
            [],
            '',
            false
        );
        $invoiceMock->expects($this->exactly(2))->method('getBaseRewardCurrencyAmount')->will($this->returnValue(100));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getInvoice'], [], '', false);
        $eventMock->expects($this->once())->method('getInvoice')->will($this->returnValue($invoiceMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            [
                'getRwrdCurrencyAmountInvoiced',
                'getBaseRwrdCrrncyAmtInvoiced',
                'setRwrdCurrencyAmountInvoiced',
                'setBaseRwrdCrrncyAmtInvoiced',
                '__wakeup'
            ],
            [],
            '',
            false
        );
        $orderMock->expects($this->once())->method('getRwrdCurrencyAmountInvoiced')->will($this->returnValue(50));
        $orderMock->expects($this->once())->method('getBaseRwrdCrrncyAmtInvoiced')->will($this->returnValue(50));
        $orderMock->expects($this->once())
            ->method('setRwrdCurrencyAmountInvoiced')
            ->with(100)
            ->will($this->returnSelf());
        $orderMock->expects($this->once())
            ->method('setBaseRwrdCrrncyAmtInvoiced')
            ->with(150)
            ->will($this->returnSelf());

        $invoiceMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $invoiceMock->expects($this->once())->method('getRewardCurrencyAmount')->will($this->returnValue(50));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
