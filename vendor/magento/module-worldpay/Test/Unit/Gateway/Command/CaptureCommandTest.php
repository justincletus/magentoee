<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Worldpay\Test\Unit\Gateway\Command;

use Magento\Worldpay\Gateway\Command\CaptureCommand;

class CaptureCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CaptureCommand
     */
    protected $command;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp()
    {
        $this->command = new CaptureCommand(
            $this->getMockBuilder(
                'Magento\Payment\Gateway\Request\BuilderInterface'
            )->getMockForAbstractClass(),
            $this->getMockBuilder(
                'Magento\Payment\Gateway\Http\TransferFactoryInterface'
            )->getMockForAbstractClass(),
            $this->getMockBuilder(
                'Magento\Payment\Gateway\Http\ClientInterface'
            )->getMockForAbstractClass(),
            $this->getMockBuilder(
                'Magento\Payment\Gateway\Response\HandlerInterface'
            )->getMockForAbstractClass(),
            $this->getMockBuilder(
                'Magento\Payment\Gateway\Validator\ValidatorInterface'
            )->getMockForAbstractClass()
        );
    }

    public function testExecuteNotOrderPayment()
    {
        $paymentDO = $this->getMockBuilder(
            'Magento\Payment\Gateway\Data\PaymentDataObjectInterface'
        )
            ->getMockForAbstractClass();
        $paymentInfo = $this->getMockBuilder('Magento\Payment\Model\InfoInterface')
            ->getMockForAbstractClass();

        $paymentDO->expects(static::once())
            ->method('getPayment')
            ->willReturn($paymentInfo);
        $paymentInfo->expects(static::never())
            ->method('getAuthorizationTransaction');


        $this->command->execute(
            [
                'payment' => $paymentDO
            ]
        );
    }

    public function testExecuteNoAuthTransaction()
    {
        $paymentDO = $this->getMockBuilder(
            'Magento\Payment\Gateway\Data\PaymentDataObjectInterface'
        )
            ->getMockForAbstractClass();
        $paymentInfo = $this->getMockBuilder('Magento\Sales\Model\Order\Payment')
            ->disableOriginalConstructor()
            ->getMock();

        $paymentDO->expects(static::once())
            ->method('getPayment')
            ->willReturn($paymentInfo);
        $paymentInfo->expects(static::once())
            ->method('getAuthorizationTransaction')
            ->willReturn(false);


        $this->command->execute(
            [
                'payment' => $paymentDO
            ]
        );
    }
}
