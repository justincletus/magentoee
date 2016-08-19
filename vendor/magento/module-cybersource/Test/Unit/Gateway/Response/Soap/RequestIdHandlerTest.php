<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Cybersource\Test\Unit\Gateway\Response\Soap;

use Magento\Cybersource\Gateway\Response\Soap\RequestIdHandler;

/**
 * Class RequestIdHandlerTest
 */
class RequestIdHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $paymentDO = $this->getMockBuilder('Magento\Payment\Gateway\Data\PaymentDataObjectInterface')
            ->getMockForAbstractClass();
        $paymentInfo = $this->getMockBuilder('Magento\Payment\Model\InfoInterface')
            ->getMockForAbstractClass();
        $handlingSubject = [
            'payment' => $paymentDO
        ];
        $response = [
            RequestIdHandler::REQUEST_ID => '1'
        ];

        $paymentDO->expects(static::once())
            ->method('getPayment')
            ->willReturn($paymentInfo);

        $paymentInfo->expects(static::never())
            ->method('setTransactionId');
        $paymentInfo->expects(static::never())
            ->method('setIsTransactionClosed');
        $paymentInfo->expects(static::never())
            ->method('setShouldCloseParentTransaction');

        $handler = new RequestIdHandler();
        $handler->handle($handlingSubject, $response);
    }

    public function testHandleOrderPayment()
    {
        $paymentDO = $this->getMockBuilder('Magento\Payment\Gateway\Data\PaymentDataObjectInterface')
            ->getMockForAbstractClass();
        $paymentInfo = $this->getMockBuilder('Magento\Sales\Model\Order\Payment')
            ->disableOriginalConstructor()
            ->setMethods(['setTransactionId', 'setShouldCloseParentTransaction', 'setIsTransactionClosed'])
            ->getMock();
        $handlingSubject = [
            'payment' => $paymentDO
        ];
        $response = [
            RequestIdHandler::REQUEST_ID => '1'
        ];

        $paymentDO->expects(static::exactly(2))
            ->method('getPayment')
            ->willReturn($paymentInfo);

        $paymentInfo->expects(static::once())
            ->method('setTransactionId')
            ->with($response[RequestIdHandler::REQUEST_ID]);
        $paymentInfo->expects(static::once())
            ->method('setIsTransactionClosed')
            ->with(false);
        $paymentInfo->expects(static::once())
            ->method('setShouldCloseParentTransaction')
            ->with(false);

        $handler = new RequestIdHandler();
        $handler->handle($handlingSubject, $response);
    }
}
