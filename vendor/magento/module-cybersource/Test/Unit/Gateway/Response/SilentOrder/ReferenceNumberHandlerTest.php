<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Cybersource\Test\Unit\Gateway\Response\SilentOrder;

use Magento\Cybersource\Gateway\Request\SilentOrder\TransactionDataBuilder;
use Magento\Cybersource\Gateway\Response\SilentOrder\ReferenceNumberHandler;

class ReferenceNumberHandlerTest extends \PHPUnit_Framework_TestCase
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
            'req_' . TransactionDataBuilder::REFERENCE_NUMBER => '1'
        ];

        $paymentDO->expects(static::atLeastOnce())
            ->method('getPayment')
            ->willReturn($paymentInfo);
        $paymentInfo->expects(static::once())
            ->method('setAdditionalInformation')
            ->with(
                TransactionDataBuilder::REFERENCE_NUMBER,
                '1'
            );

        $handler = new ReferenceNumberHandler();
        $handler->handle($handlingSubject, $response);
    }
}
