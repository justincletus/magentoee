<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Worldpay\Test\Unit\Gateway\Response;

use Magento\Worldpay\Gateway\Response\PaymentDetailsHandler;

class PaymentDetailsHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $response = [
            'transId' => '10001',
            'cardType' => 'Visa'
        ];

        $paymentDO = $this->getMockBuilder(
            'Magento\Payment\Gateway\Data\PaymentDataObjectInterface'
        )
            ->getMockForAbstractClass();
        $paymentInfo = $this->getMockBuilder(
            'Magento\Sales\Model\Order\Payment'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $paymentDO->expects(static::any())
            ->method('getPayment')
            ->willReturn($paymentInfo);
        $paymentInfo->expects(static::once())
            ->method('setTransactionId')
            ->with('10001');
        $paymentInfo->expects(static::once())
            ->method('setLastTransId')
            ->with('10001');
        $paymentInfo->expects(static::once())
            ->method('setAdditionalInformation')
            ->with('cc_type', 'Visa');
        $paymentInfo->expects(static::once())
            ->method('setIsTransactionClosed')
            ->with(false);

        $handler = new PaymentDetailsHandler();
        $handler->handle(
            ['payment' => $paymentDO],
            $response
        );
    }
}
