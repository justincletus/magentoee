<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Cybersource\Test\Unit\Gateway\Response\Soap;

use Magento\Cybersource\Gateway\Response\Soap\SubscriptionIdHandler;
use Magento\Cybersource\Gateway\Request\Soap\AuthorizeDataBuilder;

class SubscriptionIdHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $paymentDO = $this->getMockBuilder('Magento\Payment\Gateway\Data\PaymentDataObjectInterface')
            ->getMockForAbstractClass();
        $paymentInfo = $this->getMockBuilder('Magento\Payment\Model\InfoInterface')
            ->setMethods(['setIsTransactionPending', 'setIsFraudDetected'])
            ->getMockForAbstractClass();
        $handlingSubject = [
            'payment' => $paymentDO
        ];
        $response = [
            'paySubscriptionCreateReply' => [
                AuthorizeDataBuilder::SUBSCRIPTION_ID => '1111',
            ]
        ];

        $paymentDO->expects(static::atLeastOnce())
            ->method('getPayment')
            ->willReturn($paymentInfo);
        $paymentInfo->expects(static::once())
            ->method('setAdditionalInformation')
            ->with(
                AuthorizeDataBuilder::SUBSCRIPTION_ID,
                '1111'
            );

        $handler = new SubscriptionIdHandler();
        $handler->handle($handlingSubject, $response);
    }
}
