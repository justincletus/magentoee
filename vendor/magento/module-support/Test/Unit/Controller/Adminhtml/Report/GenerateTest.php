<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Controller\Adminhtml\Report;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

class GenerateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var \Magento\Backend\Model\View\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Support\Model\ReportFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reportFactoryMock;

    /**
     * @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Support\Controller\Adminhtml\Report\Generate
     */
    protected $generateAction;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->requestMock = $this->getMock(
            'Magento\Framework\App\RequestInterface',
            [
                'isPost', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName',
                'getParam', 'setParams', 'getParams', 'getCookie', 'isSecure', 'getPost'
            ]
        );
        $this->messageManagerMock = $this->getMock('Magento\Framework\Message\ManagerInterface');

        $this->reportFactoryMock = $this->getMock('Magento\Support\Model\ReportFactory', ['create'], [], '', false);

        $this->resultRedirectMock = $this->getMock('Magento\Backend\Model\View\Result\Redirect', [], [], '', false);
        $this->resultFactoryMock = $this->getMock('Magento\Framework\Controller\ResultFactory', [], [], '', false);
        $this->resultFactoryMock->expects($this->any())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($this->resultRedirectMock);

        $this->contextMock = $this->getMock('Magento\Backend\App\Action\Context', [], [], '', false);
        $this->contextMock->expects($this->any())
            ->method('getMessageManager')
            ->willReturn($this->messageManagerMock);
        $this->contextMock->expects($this->any())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);
        $this->contextMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->requestMock);

        $this->generateAction = $this->objectManagerHelper->getObject(
            'Magento\Support\Controller\Adminhtml\Report\Generate',
            [
                'context' => $this->contextMock,
                'reportFactory' => $this->reportFactoryMock
            ]
        );
    }

    /**
     * @return void
     */
    public function testExecuteRequestNonPost()
    {
        $this->requestMock->expects($this->once())
            ->method('isPost')
            ->willReturn(false);
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/index')
            ->willReturnSelf();
        $this->requestMock->expects($this->never())
            ->method('getParam');

        $this->assertSame($this->resultRedirectMock, $this->generateAction->execute());
    }

    /**
     * @return void
     */
    public function testExecuteWithoutReportGroups()
    {
        $this->requestMock->expects($this->once())
            ->method('isPost')
            ->willReturn(true);
        $this->requestMock->expects($this->once())
            ->method('getPost')
            ->with('general')
            ->willReturn(null);
        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with(__('No groups were specified to generate system report.'))
            ->willReturnSelf();
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/create')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->generateAction->execute());
    }

    /**
     * @return void
     */
    public function testExecuteMainFlow()
    {
        $reportGroups = 'testReport';
        $this->requestMock->expects($this->once())
            ->method('isPost')
            ->willReturn(true);
        $this->requestMock->expects($this->once())
            ->method('getPost')
            ->with('general')
            ->willReturn(['report_groups' => $reportGroups]);

        /** @var \Magento\Support\Model\Report|\PHPUnit_Framework_MockObject_MockObject $reportMock */
        $reportMock = $this->getMock('Magento\Support\Model\Report', [], [], '', false);
        $reportMock->expects($this->once())
            ->method('generate')
            ->with($reportGroups);
        $reportMock->expects($this->once())
            ->method('save');
        $this->reportFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($reportMock);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccess')
            ->with(__('The system report has been generated.'))
            ->willReturnSelf();

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/index')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->generateAction->execute());
    }

    /**
     * @return void
     */
    public function testExecuteWithLocalizedException()
    {
        $errorMsg = 'Test error';
        $this->requestMock->expects($this->once())
            ->method('isPost')
            ->willReturn(true);
        $this->requestMock->expects($this->once())
            ->method('getPost')
            ->with('general')
            ->willReturn(['report_groups' => 'report']);
        $this->reportFactoryMock->expects($this->once())
            ->method('create')
            ->willThrowException(new LocalizedException(__($errorMsg)));
        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with($errorMsg)
            ->willReturnSelf();
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/index')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->generateAction->execute());
    }

    /**
     * @return void
     */
    public function testExecuteWithException()
    {
        $errorMsg = 'Test error';
        $exception = new \Exception($errorMsg);
        $this->requestMock->expects($this->once())
            ->method('isPost')
            ->willReturn(true);
        $this->requestMock->expects($this->once())
            ->method('getPost')
            ->with('general')
            ->willReturn(['report_groups' => 'report']);
        $this->reportFactoryMock->expects($this->once())
            ->method('create')
            ->willThrowException($exception);
        $this->messageManagerMock->expects($this->once())
            ->method('addException')
            ->with(
                $exception,
                __('An error occurred while the system report was being created. Please review the log and try again.')
            )
            ->willReturnSelf();
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/index')
            ->willReturnSelf();

        $this->generateAction = $this->objectManagerHelper->getObject(
            'Magento\Support\Controller\Adminhtml\Report\Generate',
            [
                'context' => $this->contextMock,
                'reportFactory' => $this->reportFactoryMock
            ]
        );

        $this->assertSame($this->resultRedirectMock, $this->generateAction->execute());
    }
}
