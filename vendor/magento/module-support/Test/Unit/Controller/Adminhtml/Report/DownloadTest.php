<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Controller\Adminhtml\Report;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DownloadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Support\Controller\Adminhtml\Report\Download
     */
    protected $downloadAction;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\View\LayoutFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var \Magento\Backend\Model\View\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectMock;

    /**
     * @var \Magento\Support\Model\ReportFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reportFactoryMock;

    /**
     * @var \Magento\Support\Model\Report|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reportMock;

    /**
     * @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->messageManagerMock = $this->getMock('Magento\Framework\Message\ManagerInterface');
        $this->layoutFactory = $this->getMock('Magento\Framework\View\LayoutFactory', [], [], '', false);
        $this->fileFactory = $this->getMock('Magento\Framework\App\Response\Http\FileFactory', [], [], '', false);

        $this->reportMock = $this->getMock('Magento\Support\Model\Report', [], [], '', false);
        $this->reportFactoryMock = $this->getMock('Magento\Support\Model\ReportFactory', ['create'], [], '', false);
        $this->reportFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->reportMock);

        $this->resultRedirectMock = $this->getMock('Magento\Backend\Model\View\Result\Redirect', [], [], '', false);
        $this->resultRedirectMock->expects($this->any())
            ->method('setPath')
            ->with('*/*/index')
            ->willReturnSelf();
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

        $this->downloadAction = $this->objectManagerHelper->getObject(
            'Magento\Support\Controller\Adminhtml\Report\Download',
            [
                'context' => $this->contextMock,
                'reportFactory' => $this->reportFactoryMock,
                'layoutFactory' => $this->layoutFactory,
                'fileFactory' => $this->fileFactory
            ]
        );
    }

    /**
     * @param int $id
     * @return void
     */
    protected function setIdReport($id)
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($id);
        $this->reportMock->expects($this->once())
            ->method('getId')
            ->willReturn($id);
    }

    /**
     * @return void
     */
    public function testExecuteMainFlow()
    {
        $id = 1;
        $fileName = 'report.html';
        $content = 'Some text';

        $this->setIdReport($id);
        $this->reportMock->expects($this->once())
            ->method('load')
            ->with($id)
            ->willReturnSelf();
        $this->reportMock->expects($this->once())
            ->method('getFileNameForReportDownload')
            ->willReturn($fileName);

        /** @var \Magento\Framework\View\Element\AbstractBlock|\PHPUnit_Framework_MockObject_MockObject $block */
        $block = $this->getMock('\Magento\Framework\View\Element\AbstractBlock', [], [], '', false);
        $block->expects($this->once())
            ->method('setData')
            ->with(['report' => $this->reportMock])
            ->willReturnSelf();
        $block->expects($this->once())
            ->method('toHtml')
            ->willReturn($content);

        /** @var \Magento\Framework\View\Layout|\PHPUnit_Framework_MockObject_MockObject $layout */
        $layout = $this->getMock('Magento\Framework\View\Layout', [], [], '', false);
        $layout->expects($this->once())
            ->method('createBlock')
            ->with('Magento\Support\Block\Adminhtml\Report\Export\Html', 'report.export.html')
            ->willReturn($block);

        $this->layoutFactory->expects($this->once())
            ->method('create')
            ->willReturn($layout);

        $this->fileFactory->expects($this->once())
            ->method('create')
            ->with(
                $fileName,
                $content,
                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR
            );

        $this->downloadAction->execute();
    }

    /**
     * @return void
     */
    public function testExecuteWithoutReport()
    {
        $id = 0;
        $this->setIdReport($id);
        $this->reportMock->expects($this->never())->method('load');

        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with(__('Requested system report no longer exists.'))
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->downloadAction->execute());
    }

    /**
     * @return void
     */
    public function testExecuteWithLocalizedException()
    {
        $e = new LocalizedException(__('Test error'));
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->willThrowException($e);
        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with($e)
            ->willReturnSelf();
        $this->assertSame($this->resultRedirectMock, $this->downloadAction->execute());
    }

    /**
     * @return void
     */
    public function testExecuteWithException()
    {
        $e = new \Exception();
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->willThrowException($e);
        $this->messageManagerMock->expects($this->once())
            ->method('addException')
            ->with($e, __('Unable to read system report data to display.'))
            ->willReturnSelf();
        $this->assertSame($this->resultRedirectMock, $this->downloadAction->execute());
    }
}
