<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Eway\Test\Unit\Gateway\Request;

use Magento\Eway\Gateway\Request\CardDetailsDataBuilder;
use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;

class CardDetailsDataBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CardDetailsDataBuilder
     */
    private $builder;

    public function setUp()
    {
        $this->builder = new CardDetailsDataBuilder();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Payment data object should be provided
     */
    public function testBuildReadPaymentException()
    {
        $buildSubject = [
            'payment' => null,
        ];

        $this->builder->build($buildSubject);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Order payment should be provided.
     */
    public function testBuildAssertOrderPaymentException()
    {
        $paymentDO = $this->getMockBuilder('Magento\Payment\Gateway\Data\PaymentDataObjectInterface')
            ->getMockForAbstractClass();
        $order = $this->getMockBuilder('Magento\Payment\Gateway\Data\OrderAdapterInterface')
            ->getMockForAbstractClass();
        $payment = $this->getMockBuilder('Magento\Quote\Model\Quote\Payment')
            ->disableOriginalConstructor()
            ->getMock();

        $paymentDO->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);
        $paymentDO->expects($this->once())
            ->method('getPayment')
            ->willReturn($payment);

        $buildSubject = [
            'payment' => $paymentDO,
        ];

        $this->builder->build($buildSubject);
    }

    /**
     * @param array $billingAddressData
     * @param array $paymentData
     * @param array $expectedResult
     *
     * @dataProvider dataProviderBuild
     */
    public function testBuild($billingAddressData, $paymentData, $expectedResult)
    {
        $paymentDO = $this->getMockBuilder('Magento\Payment\Gateway\Data\PaymentDataObjectInterface')
            ->getMockForAbstractClass();
        $order = $this->getMockBuilder('Magento\Payment\Gateway\Data\OrderAdapterInterface')
            ->getMockForAbstractClass();
        $billingAddress = $this->getMockBuilder('Magento\Payment\Gateway\Data\AddressAdapterInterface')
            ->getMockForAbstractClass();
        $payment = $this->getMockBuilder('\Magento\Sales\Model\Order\Payment')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMock();


        $paymentDO->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);
        $order->expects($this->once())
            ->method('getBillingAddress')
            ->willReturn($billingAddress);
        $paymentDO->expects($this->once())
            ->method('getPayment')
            ->willReturn($payment);

        $billingAddress->expects($this->once())
            ->method('getFirstname')
            ->willReturn($billingAddressData['first_name']);
        $billingAddress->expects($this->once())
            ->method('getLastname')
            ->willReturn($billingAddressData['last_name']);
        $payment->expects($this->exactly(7))
            ->method('getData')
            ->willReturnMap(
                [
                    ['cc_number', null, $paymentData['cc_number']],
                    ['cc_exp_month', null, $paymentData['cc_exp_month']],
                    ['cc_exp_year', null, $paymentData['cc_exp_year']],
                    ['cc_ss_start_month', null, $paymentData['cc_ss_start_month']],
                    ['cc_ss_start_year', null, $paymentData['cc_ss_start_year']],
                    ['cc_ss_issue', null, $paymentData['cc_ss_issue']],
                    ['cc_cid', null, $paymentData['cc_cid']]
                ]
            );


        $buildSubject = [
            'payment' => $paymentDO,
        ];

        $this->assertEquals($expectedResult, $this->builder->build($buildSubject));
    }

    /**
     * @return array
     */
    public function dataProviderBuild()
    {
        return [
            [
                [
                    'first_name' => 'John',
                    'last_name' => 'Smith'
                ],
                [
                    'cc_number' => '4444333322221111',
                    'cc_exp_month' => '1',
                    'cc_exp_year' => '2020',
                    'cc_ss_start_month' => '11',
                    'cc_ss_start_year' => '2010',
                    'cc_ss_issue' => '1',
                    'cc_cid' => '123'
                ],
                [
                    'Customer' => [
                        'CardDetails' => [
                            'Name' => 'John Smith',
                            'Number' => '4444333322221111',
                            'ExpiryMonth' => '01',
                            'ExpiryYear' => '20',
                            'StartMonth' => '11',
                            'StartYear' => '10',
                            'IssueNumber' => '1',
                            'CVN' => '123'
                        ]
                    ]
                ]
            ]
        ];
    }
}
