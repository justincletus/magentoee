<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Cybersource\Test\Unit\Gateway\Command\SilentOrder\Token;

use Magento\Cybersource\Gateway\Command\SilentOrder\Token\CreateCommand;

/**
 * Class CreateCommandTest
 * @package Magento\Cybersource\Gateway\Command\SilentOrder\Token
 */
class CreateCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CreateCommand
     */
    protected $command;

    /**
     * @var \Magento\Payment\Gateway\Request\BuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $builder;

    /**
     * @var \Magento\Payment\Gateway\Command\Result\ArrayResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $arrayResultFactory;

    /**
     * @var \Magento\Payment\Model\Method\Logger|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    protected function setUp()
    {
        $this->builder = $this->getMockBuilder('Magento\Payment\Gateway\Request\BuilderInterface')
            ->getMockForAbstractClass();
        $this->arrayResultFactory = $this->getMockBuilder(
            'Magento\Payment\Gateway\Command\Result\ArrayResultFactory'
        )
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->logger = $this->getMockBuilder('Magento\Payment\Model\Method\Logger')
            ->disableOriginalConstructor()
            ->getMock();
        $this->command = new CreateCommand($this->builder, $this->arrayResultFactory, $this->logger);
    }

    public function testExecute()
    {
        $commandSubject['amount'] = 20;
        $commandSubject['payment'] = $this->getMockBuilder(
            'Magento\Payment\Gateway\Data\PaymentDataObjectInterface'
        )
            ->getMockForAbstractClass();
        $arrayResult = $this->getMockBuilder('Magento\Payment\Gateway\Command\Result\ArrayResult')
            ->disableOriginalConstructor()
            ->getMock();

        $buildResult = ['somefield' => 'somevalue'];
        $this->builder->expects($this->once())
            ->method('build')
            ->with($commandSubject)
            ->willReturn($buildResult);
        $this->logger->expects($this->once())
            ->method('debug')
            ->with(['payment_token_request' => $buildResult]);
        $this->arrayResultFactory->expects($this->once())
            ->method('create')
            ->with(['array' => $buildResult])
            ->willReturn($arrayResult);

        $this->assertInstanceOf(
            'Magento\Payment\Gateway\Command\Result\ArrayResult',
            $this->command->execute($commandSubject)
        );
    }
}
