<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Controller\Adminhtml\Backup;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class DeleteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Support\Model\BackupFactory|\PHPUnit_Framework_MockObject_MockObject;
     */
    protected $backupFactoryMock;

    /**
     * @var \Magento\Support\Model\Backup|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backupMock;

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
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Support\Controller\Adminhtml\Backup\Delete
     */
    protected $deleteAction;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * @var int
     */
    protected $id = 1;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->messageManagerMock = $this->getMock('Magento\Framework\Message\ManagerInterface');
        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id', 0)
            ->willReturn($this->id);

        $this->backupMock = $this->getMock('Magento\Support\Model\Backup', [], [], '', false);
        $this->backupMock->expects($this->once())
            ->method('load')
            ->with($this->id)
            ->willReturnSelf();
        $this->backupFactoryMock = $this->getMock('Magento\Support\Model\BackupFactory', ['create'], [], '', false);
        $this->backupFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->backupMock);

        $this->redirectMock = $this->getMock('Magento\Backend\Model\View\Result\Redirect', [], [], '', false);
        $this->redirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/index')
            ->willReturnSelf();
        $this->resultFactoryMock = $this->getMock('Magento\Framework\Controller\ResultFactory', [], [], '', false);
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT, [])
            ->willReturn($this->redirectMock);


        $this->context = $this->objectManagerHelper->getObject(
            'Magento\Backend\App\Action\Context',
            [
                'messageManager' => $this->messageManagerMock,
                'resultFactory' => $this->resultFactoryMock,
                'request' => $this->requestMock
            ]
        );
        $this->deleteAction = $this->objectManagerHelper->getObject(
            'Magento\Support\Controller\Adminhtml\Backup\Delete',
            [
                'context' => $this->context,
                'backupFactory' => $this->backupFactoryMock
            ]
        );
    }

    /**
     * @return void
     */
    public function testExecute()
    {
        $this->backupMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->id);
        $this->backupMock->expects($this->once())
            ->method('delete')
            ->willReturnSelf();
        $this->messageManagerMock->expects($this->once())
            ->method('addSuccess')
            ->with(__('The backup has been deleted.'))
            ->willReturnSelf();

        $this->assertSame($this->redirectMock, $this->deleteAction->execute());
    }

    /**
     * @return void
     */
    public function testExecuteWrongId()
    {
        $this->backupMock->expects($this->once())
            ->method('getId')
            ->willReturn(0);
        $this->backupMock->expects($this->never())->method('delete');
        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with(__('Wrong param id'))
            ->willReturnSelf();

        $this->assertSame($this->redirectMock, $this->deleteAction->execute());
    }

    /**
     * @return void
     */
    public function testExecuteWithLocalizedException()
    {
        $eText = 'Some error';
        $e = new \Magento\Framework\Exception\LocalizedException(__($eText));
        $this->backupMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->id);
        $this->backupMock->expects($this->once())
            ->method('delete')
            ->willThrowException($e);
        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with($eText)
            ->willReturnSelf();

        $this->assertSame($this->redirectMock, $this->deleteAction->execute());
    }

    /**
     * @return void
     */
    public function testExecuteWithException()
    {
        $e = new \Exception();
        $this->backupMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->id);
        $this->backupMock->expects($this->once())
            ->method('delete')
            ->willThrowException($e);
        $this->messageManagerMock->expects($this->once())
            ->method('addException')
            ->with($e, __('Cannot delete backup'))
            ->willReturnSelf();

        $this->assertSame($this->redirectMock, $this->deleteAction->execute());
    }
}
