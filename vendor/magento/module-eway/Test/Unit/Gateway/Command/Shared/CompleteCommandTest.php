<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Eway\Test\Unit\Gateway\Command\Shared;

use Magento\Eway\Gateway\Command\Shared\CompleteCommand;
use Magento\Eway\Gateway\Command\Shared\UpdateDetailsCommand;
use Magento\Eway\Gateway\Command\Shared\UpdateOrderCommand;

class CompleteCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CompleteCommand
     */
    private $command;

    /**
     * @var UpdateDetailsCommand|\PHPUnit_Framework_MockObject_MockObject
     */
    private $updateDetailsCommandMock;

    /**
     * @var UpdateOrderCommand|\PHPUnit_Framework_MockObject_MockObject
     */
    private $updateOrderCommandMock;

    public function setUp()
    {
        $this->updateDetailsCommandMock = $this
            ->getMockBuilder('Magento\Eway\Gateway\Command\Shared\UpdateDetailsCommand')
            ->disableOriginalConstructor()
            ->getMock();

        $this->updateOrderCommandMock = $this
            ->getMockBuilder('Magento\Eway\Gateway\Command\Shared\UpdateOrderCommand')
            ->disableOriginalConstructor()
            ->getMock();

        $this->command = new CompleteCommand(
            $this->updateDetailsCommandMock,
            $this->updateOrderCommandMock
        );
    }

    public function testExecute()
    {
        $commandSubject = ['access_code' => 'access_code'];

        $this->updateDetailsCommandMock->expects($this->once())
            ->method('execute')
            ->with($commandSubject);
        $this->updateOrderCommandMock->expects($this->once())
            ->method('execute')
            ->with($commandSubject);

        $this->command->execute($commandSubject);
    }
}
