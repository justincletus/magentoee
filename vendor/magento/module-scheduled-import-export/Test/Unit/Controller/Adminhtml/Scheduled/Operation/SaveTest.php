<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ScheduledImportExport\Test\Unit\Controller\Adminhtml\Scheduled\Operation;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\Controller\ResultFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation\Save */
    protected $save;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /** @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject */
    protected $registry;

    /** @var \Magento\Framework\App\Console\Request|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    /** @var \Magento\Framework\App\Console\Response|\PHPUnit_Framework_MockObject_MockObject */
    protected $response;

    /** @var \Magento\Framework\Message\Manager|\PHPUnit_Framework_MockObject_MockObject */
    protected $messageManager;

    /** @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $session;

    /** @var \Magento\Framework\App\ActionFlag|\PHPUnit_Framework_MockObject_MockObject */
    protected $actionFlag;

    /** @var \Magento\Backend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $backendHelper;

    /** @var  \Magento\ScheduledImportExport\Model\Scheduled\Operation|\PHPUnit_Framework_MockObject_MockObject */
    protected $operation;

    /** @var \Magento\ScheduledImportExport\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $scheduledHelper;

    /**
     * @var \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var \Magento\Backend\Model\View\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectMock;

    protected function setUp()
    {
        $this->request = $this->getMock(
            'Magento\Framework\App\Console\Request',
            ['getParam', 'isPost', 'getPostValue'],
            [],
            '',
            false
        );
        $this->messageManager = $this->getMock('Magento\Framework\Message\Manager', [], [], '', false);
        $this->session = $this->getMock('Magento\Backend\Model\Session', [], [], '', false);
        $this->actionFlag = $this->getMock('Magento\Framework\App\ActionFlag', [], [], '', false);
        $this->backendHelper = $this->getMock('\Magento\Backend\Helper\Data', [], [], '', false);
        $this->registry = $this->getMock('Magento\Framework\Registry');

        $operationFactory = $this->getMock(
            'Magento\ScheduledImportExport\Model\Scheduled\OperationFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->operation = $this->getMock(
            'Magento\ScheduledImportExport\Model\Scheduled\Operation',
            [],
            [],
            '',
            false
        );
        $operationFactory->expects($this->any())->method('create')->willReturn($this->operation);
        $this->scheduledHelper = $this->getMock('Magento\ScheduledImportExport\Helper\Data', [], [], '', false);
        $this->resultFactoryMock = $this->getMockBuilder('Magento\Framework\Controller\ResultFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultRedirectMock = $this->getMockBuilder('Magento\Backend\Model\View\Result\Redirect')
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultFactoryMock->expects($this->any())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT, [])
            ->willReturn($this->resultRedirectMock);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->context = $this->objectManagerHelper->getObject(
            'Magento\Backend\App\Action\Context',
            [
                'request' => $this->request,
                'messageManager' => $this->messageManager,
                'session' => $this->session,
                'actionFlag' => $this->actionFlag,
                'helper' => $this->backendHelper,
                'resultFactory' => $this->resultFactoryMock
            ]
        );
        $this->save = $this->objectManagerHelper->getObject(
            'Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation\Save',
            [
                'context' => $this->context,
                'coreRegistry' => $this->registry,
                'operationFactory' => $operationFactory,
                'dataHelper' => $this->scheduledHelper
            ]
        );
    }

    public function testExecuteError()
    {
        $this->request->expects($this->once())->method('isPost')->willReturn(true);
        $this->request->expects($this->once())->method('getPostValue')->willReturn([]);
        $this->messageManager->expects($this->once())->method('addError');
        $this->messageManager->expects($this->never())->method('addSuccess');
        $this->assertSame($this->resultRedirectMock, $this->save->execute());
    }

    public function testExecuteSuccess()
    {
        $this->request->expects($this->once())->method('isPost')->willReturn(true);
        $this->request->expects($this->once())->method('getPostValue')->willReturn([
            'id' => 1,
            'operation_type' => 'sometype',
            'start_time' => [12, 15]
        ]);
        $this->operation->expects($this->once())->method('setData');
        $this->operation->expects($this->once())->method('save');
        $this->messageManager->expects($this->never())->method('addError');
        $successMessage = 'Some sucess message';
        $this->scheduledHelper->expects($this->once())->method('getSuccessSaveMessage')->willReturn(
            $successMessage
        );
        $this->messageManager->expects($this->once())->method('addSuccess')->with($successMessage);
        $this->assertSame($this->resultRedirectMock, $this->save->execute());
    }
}
