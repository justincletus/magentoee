<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\WebsiteRestriction\Test\Unit\Observer;

class RestrictWebsiteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\WebsiteRestriction\Model\Observer\RestrictWebsite
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $restrictorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $controllerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dispatchResultMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observer;

    protected function setUp()
    {
        $this->markTestIncomplete();
        $this->configMock = $this->getMock('Magento\WebsiteRestriction\Model\ConfigInterface');
        $this->observer = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $this->controllerMock = $this->getMock('Magento\Framework\App\Action\Action', [], [], '', false);
        $this->requestMock = $this->getMock('\Magento\Framework\App\RequestInterface');

        $eventMock = $this->getMock('Magento\Framework\Event', ['getControllerAction', 'getRequest'], [], '', false);
        $eventMock->expects($this->once())
            ->method('getControllerAction')
            ->will(
                $this->returnValue($this->controllerMock)
            );

        $eventMock->expects($this->any())
            ->method('getRequest')
            ->will(
                $this->returnValue($this->requestMock)
            );

        $this->observer->expects($this->any())->method('getEvent')->will($this->returnValue($eventMock));

        $this->restrictorMock = $this->getMock('\Magento\WebsiteRestriction\Model\Restrictor', [], [], '', false);
        $this->dispatchResultMock = $this->getMock('\Magento\Framework\DataObject',
            ['getCustomerLoggedIn', 'getShouldProceed'],
            [],
            '',
            false
        );

        $eventManagerMock = $this->getMock('\Magento\Framework\Event\ManagerInterface');
        $eventManagerMock->expects($this->once())->method('dispatch')->with(
            'websiterestriction_frontend',
            ['controller' => $this->controllerMock, 'result' => $this->dispatchResultMock]
        );

        $factoryMock = $this->getMock('\Magento\Framework\DataObject\Factory', [], [], '', false);
        $factoryMock->expects($this->once())
            ->method('create')
            ->with(['should_proceed' => true, 'customer_logged_in' => false])
            ->will($this->returnValue($this->dispatchResultMock));

        $this->model = new \Magento\WebsiteRestriction\Observer\RestrictWebsite(
            $this->configMock,
            $eventManagerMock,
            $this->restrictorMock,
            $factoryMock
        );
    }

    public function testExecuteSuccess()
    {
        $this->dispatchResultMock->expects($this->any())->method('getShouldProceed')->will($this->returnValue(true));
        $this->configMock->expects($this->any())->method('isRestrictionEnabled')->will($this->returnValue(true));
        $this->dispatchResultMock->expects($this->once())->method('getCustomerLoggedIn')->will($this->returnValue(1));

        $responseMock = $this->getMock('\Magento\Framework\App\ResponseInterface');
        $this->controllerMock->expects($this->once())->method('getResponse')->will($this->returnValue($responseMock));

        $this->restrictorMock->expects($this->once())->method('restrict')->with($this->requestMock, $responseMock, 1);
        $this->model->execute($this->observer);
    }

    public function testExecuteWithDisabledRestriction()
    {
        $this->configMock->expects($this->any())->method('isRestrictionEnabled')->will($this->returnValue(false));
        $this->restrictorMock->expects($this->never())->method('restrict');
        $this->model->execute($this->observer);
    }

    public function testExecuteWithNotShouldProceed()
    {
        $this->dispatchResultMock->expects($this->any())->method('getShouldProceed')->will($this->returnValue(false));
        $this->restrictorMock->expects($this->never())->method('restrict');
        $this->model->execute($this->observer);
    }
}
