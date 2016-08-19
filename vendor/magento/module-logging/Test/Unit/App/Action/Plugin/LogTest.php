<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Logging\Test\Unit\App\Action\Plugin;

class LogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $processorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var Log
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \Closure
     */
    protected $closureMock;

    protected function setUp()
    {
        $this->processorMock = $this->getMock('\Magento\Logging\Model\Processor', [], [], '', false);
        $this->requestMock = $this->getMock('\Magento\Framework\App\Request\Http', [], [], '', false);
        $this->requestMock->expects(
            $this->once()
        )->method(
            'getActionName'
        )->will(
            $this->returnValue('taction')
        );
        $this->subjectMock = $this->getMock('Magento\Framework\App\ActionInterface');
        $this->closureMock = function () {
            return 'Expected';
        };
        $this->model = new \Magento\Logging\App\Action\Plugin\Log($this->processorMock);
    }

    public function testAroundDispatchWithoutForward()
    {
        $this->requestMock->expects(
            $this->once()
        )->method(
            'getFullActionName'
        )->will(
            $this->returnValue('tmodule_tcontroller_taction')
        );
        $this->processorMock->expects(
            $this->once()
        )->method(
            'initAction'
        )->with(
            'tmodule_tcontroller_taction',
            'taction'
        );
        $this->assertEquals(
            'Expected',
            $this->model->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }

    public function testAroundDispatchWithForward()
    {
        $this->requestMock->expects(
            $this->once()
        )->method(
            'getRouteName'
        )->will(
            $this->returnValue('origRoute')
        );

        $this->requestMock->expects(
            $this->once()
        )->method(
            'getBeforeForwardInfo'
        )->will(
            $this->returnValue(['controller_name' => 'origcontroller', 'action_name' => 'origaction'])
        );
        $this->processorMock->expects(
            $this->once()
        )->method(
            'initAction'
        )->with(
            'origRoute_origcontroller_origaction',
            'taction'
        );
        $this->assertEquals(
            'Expected',
            $this->model->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }

    public function testAroundDispatchWithForwardAndWithoutOriginalInfo()
    {
        $this->requestMock->expects(
            $this->once()
        )->method(
            'getRouteName'
        )->will(
            $this->returnValue('origRoute')
        );
        $this->requestMock->expects(
            $this->once()
        )->method(
            'getControllerName'
        )->will(
            $this->returnValue('requestedController')
        );
        $this->requestMock->expects(
            $this->once()
        )->method(
            'getBeforeForwardInfo'
        )->will(
            $this->returnValue(['forward'])
        );
        $this->processorMock->expects(
            $this->once()
        )->method(
            'initAction'
        )->with(
            'origRoute_requestedController_taction',
            'taction'
        );
        $this->assertEquals(
            'Expected',
            $this->model->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }
}
