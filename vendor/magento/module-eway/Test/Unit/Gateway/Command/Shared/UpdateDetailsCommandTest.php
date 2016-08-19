<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Eway\Test\Unit\Gateway\Command\Shared;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Eway\Gateway\Command\Shared\UpdateDetailsCommand;
use Magento\Eway\Gateway\Validator\Shared\AccessCodeValidator;

/**
 * Class UpdateDetailsCommandTest
 *
 * @see \Magento\Eway\Gateway\Command\Shared\UpdateDetailsCommand
 */
class UpdateDetailsCommandTest extends \PHPUnit_Framework_TestCase
{
    const ACCESS_CODE = 'test-access-code';

    const RESPONSE = 'test-response';

    const AMOUNT = 100;

    /**
     * @var UpdateDetailsCommand
     */
    private $updateDetailsCommand;

    /**
     * @var TransferFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transferFactoryMock;

    /**
     * @var ClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $clientMock;

    /**
     * @var ValidatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $validatorMock;

    /**
     * @var HandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $handlerMock;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp()
    {
        $this->transferFactoryMock = $this->getMockBuilder('Magento\Payment\Gateway\Http\TransferFactoryInterface')
            ->getMockForAbstractClass();
        $this->clientMock = $this->getMockBuilder('Magento\Payment\Gateway\Http\ClientInterface')
            ->getMockForAbstractClass();
        $this->validatorMock = $this->getMockBuilder('Magento\Payment\Gateway\Validator\ValidatorInterface')
            ->getMockForAbstractClass();
        $this->handlerMock = $this->getMockBuilder('Magento\Payment\Gateway\Response\HandlerInterface')
            ->getMockForAbstractClass();

        $this->updateDetailsCommand = new UpdateDetailsCommand(
            $this->transferFactoryMock,
            $this->clientMock,
            $this->validatorMock,
            $this->handlerMock
        );
    }

    /**
     * Run test for execute method
     *
     * @return void
     */
    public function testExecute()
    {
        list($resultMock, $commandSubject) = $this->getTestData();

        /** @var \PHPUnit_Framework_MockObject_MockObject $resultMock */
        $resultMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->handlerMock->expects($this->once())
            ->method('handle')
            ->with($commandSubject, [self::RESPONSE]);

        $this->updateDetailsCommand->execute($commandSubject);
    }

    /**
     * Run test for execute method (Exception)
     *
     * @return void
     *
     * @expectedException \Magento\Payment\Gateway\Command\CommandException
     * @expectedExceptionMessageRegExp message-1\nmessage-2
     */
    public function testExecuteException()
    {
        list($resultMock, $commandSubject) = $this->getTestData();

        /** @var \PHPUnit_Framework_MockObject_MockObject $resultMock */
        $resultMock->expects($this->once())
            ->method('isValid')
            ->willReturn(false);
        $resultMock->expects($this->once())
            ->method('getFailsDescription')
            ->willReturn(['message-1', 'message-2']);

        $this->handlerMock->expects($this->never())
            ->method('handle');

        $this->updateDetailsCommand->execute($commandSubject);
    }

    /**
     * @return array
     */
    private function getTestData()
    {
        $paymentDoMock = $this->getMockBuilder('Magento\Payment\Gateway\Data\PaymentDataObjectInterface')
            ->getMockForAbstractClass();
        $paymentMock = $this->getMockBuilder('Magento\Sales\Model\Order\Payment')
            ->disableOriginalConstructor()
            ->getMock();
        $transferOMock = $this->getMockBuilder('Magento\Payment\Gateway\Http\TransferInterface')
            ->getMockForAbstractClass();
        $orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->getMock();
        $resultMock = $this->getMockBuilder('Magento\Payment\Gateway\Validator\ResultInterface')
            ->getMockForAbstractClass();

        $commandSubject = [
            'access_code' => self::ACCESS_CODE,
            'payment' => $paymentDoMock
        ];
        $response = [self::RESPONSE];

        $paymentDoMock->expects($this->once())
            ->method('getPayment')
            ->willReturn($paymentMock);

        $paymentMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);

        $this->transferFactoryMock->expects($this->once())
            ->method('create')
            ->with([AccessCodeValidator::ACCESS_CODE => self::ACCESS_CODE])
            ->willReturn($transferOMock);

        $this->clientMock->expects($this->once())
            ->method('placeRequest')
            ->with($transferOMock)
            ->willReturn($response);

        $orderMock->expects($this->once())
            ->method('getTotalDue')
            ->willReturn(self::AMOUNT);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with(
                array_merge(
                    $commandSubject,
                    [
                        'response' => $response,
                        'amount' => self::AMOUNT
                    ]
                )
            )->willReturn($resultMock);

        return [$resultMock, $commandSubject];
    }
}
