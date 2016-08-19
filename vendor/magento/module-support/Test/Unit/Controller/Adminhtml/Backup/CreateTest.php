<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Controller\Adminhtml\Backup;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class CreateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Support\Model\ResourceModel\Backup\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backupCollectionMock;

    /**
     * @var \Magento\Support\Model\Backup|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backupModelMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \Magento\Backend\Model\View\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $redirectMock;

    /**
     * @var \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var \Magento\Support\Controller\Adminhtml\Backup\Create
     */
    protected $createAction;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->messageManagerMock = $this->getMock('Magento\Framework\Message\ManagerInterface');
        $this->backupCollectionMock = $this->getMockBuilder('Magento\Support\Model\ResourceModel\Backup\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->backupModelMock = $this->getMock('Magento\Support\Model\Backup', [], [], '', false);
        $this->redirectMock = $this->getMock('Magento\Backend\Model\View\Result\Redirect', [], [], '', false);
        $this->resultFactoryMock = $this->getMock('Magento\Framework\Controller\ResultFactory', [], [], '', false);
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT, [])
            ->willReturn($this->redirectMock);

        $this->context = $this->objectManagerHelper->getObject(
            'Magento\Backend\App\Action\Context',
            [
                'messageManager' => $this->messageManagerMock,
                'resultFactory' => $this->resultFactoryMock
            ]
        );
        $this->createAction = $this->objectManagerHelper->getObject(
            'Magento\Support\Controller\Adminhtml\Backup\Create',
            [
                'context' => $this->context,
                'backupModel' => $this->backupModelMock,
                'backupCollection' => $this->backupCollectionMock
            ]
        );
    }

    /**
     * @return void
     */
    public function testExecute()
    {
        $this->setBackupCollectionMock();

        $this->backupModelMock->expects($this->once())
            ->method('run');
        $this->backupModelMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $this->messageManagerMock->expects($this->once())
            ->method('addSuccess')
            ->with(__('The backup has been saved.'))
            ->willReturnSelf();

        $this->runTestExecute();
    }

    /**
     * @return void
     */
    public function testExecuteWithStateException()
    {
        $this->setBackupCollectionMock(1);

        $this->backupModelMock->expects($this->never())->method('run');
        $this->backupModelMock->expects($this->never())->method('save');
        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with(__('All processes should be complete'))
            ->willReturnSelf();

        $this->runTestExecute();
    }

    /**
     * @return void
     */
    public function testExecuteWithNotFoundException()
    {
        $e = new \Magento\Framework\Exception\NotFoundException(
            __('Cannot save backup. The reason is: Utility lsof not found')
        );
        $this->backupCollectionMock->expects($this->once())
            ->method('addProcessingStatusFilter')
            ->willThrowException($e);
        $this->backupModelMock->expects($this->never())->method('run');
        $this->backupModelMock->expects($this->never())->method('save');
        $this->messageManagerMock->expects($this->once())
            ->method('addException')
            ->with($e)
            ->willReturnSelf();

        $this->runTestExecute();
    }

    /**
     * @return void
     */
    public function testExecuteWithException()
    {
        $e = new \Exception();
        $this->backupCollectionMock->expects($this->once())
            ->method('addProcessingStatusFilter')
            ->willThrowException($e);
        $this->backupModelMock->expects($this->never())->method('run');
        $this->backupModelMock->expects($this->never())->method('save');
        $this->messageManagerMock->expects($this->once())
            ->method('addException')
            ->with($e, __('An error occurred while saving backup'))
            ->willReturnSelf();

        $this->runTestExecute();
    }

    /**
     * @return void
     */
    protected function runTestExecute()
    {
        $this->redirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/index')
            ->willReturnSelf();

        $this->assertSame($this->redirectMock, $this->createAction->execute());
    }

    /**
     * @param int $returnedValue
     * @return void
     */
    protected function setBackupCollectionMock($returnedValue = 0)
    {
        $this->backupCollectionMock->expects($this->once())
            ->method('addProcessingStatusFilter')
            ->willReturnSelf();
        $this->backupCollectionMock->expects($this->once())
            ->method('count')
            ->willReturn($returnedValue);
    }
}
