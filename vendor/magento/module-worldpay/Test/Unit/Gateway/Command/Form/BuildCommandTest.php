<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Worldpay\Test\Unit\Gateway\Command\Form;

use Magento\Worldpay\Gateway\Command\Form\BuildCommand;
use Magento\Worldpay\Gateway\Request\HtmlRedirect\OrderDataBuilder;

/**
 * Class BuildCommandTest
 */
class BuildCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BuildCommand
     */
    protected $buildCommand;

    /**
     * @var OrderDataBuilder|\PHPUnit_Framework_MockObject_MockObject
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

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp()
    {
        $this->builder = $this->getMockBuilder('Magento\Worldpay\Gateway\Request\HtmlRedirect\OrderDataBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->arrayResultFactory = $this->getMockBuilder(
            'Magento\Payment\Gateway\Command\Result\ArrayResultFactory'
        )
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->logger = $this->getMockBuilder('Magento\Payment\Model\Method\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $this->buildCommand = new BuildCommand($this->builder, $this->arrayResultFactory, $this->logger);
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
            ->with(['payment_form_data' => $buildResult]);
        $this->arrayResultFactory->expects($this->once())
            ->method('create')
            ->with(['array' => $buildResult])
            ->willReturn($arrayResult);

        $this->assertInstanceOf(
            'Magento\Payment\Gateway\Command\Result\ArrayResult',
            $this->buildCommand->execute($commandSubject)
        );
    }
}
