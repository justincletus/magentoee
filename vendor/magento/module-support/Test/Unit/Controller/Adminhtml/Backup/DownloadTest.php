<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Controller\Adminhtml\Backup;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class DownloadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Support\Controller\Adminhtml\Backup\Download
     */
    protected $downloadAction;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Support\Helper\Shell|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shellHelperMock;

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
     * @var \Magento\Framework\App\Response\Http\FileFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileFactoryMock;

    /**
     * @var \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var \Magento\Framework\Filesystem\Io\File|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileMock;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->messageManagerMock = $this->getMock('Magento\Framework\Message\ManagerInterface');
        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->shellHelperMock = $this->getMock('Magento\Support\Helper\Shell', [], [], '', false);
        $this->backupMock = $this->getMock('Magento\Support\Model\Backup', [], [], '', false);
        $this->backupFactoryMock = $this->getMock('Magento\Support\Model\BackupFactory', ['create'], [], '', false);
        $this->resultFactoryMock = $this->getMock('Magento\Framework\Controller\ResultFactory', [], [], '', false);
        $this->fileFactoryMock = $this->getMock('Magento\Framework\App\Response\Http\FileFactory', [], [], '', false);
        $this->fileMock = $this->getMock('Magento\Framework\Filesystem\Io\File', [], [], '', false);

        $backupId = 1;
        $backupType = 1;
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->willReturnMap([
                ['backup_id', 0, $backupId],
                ['type', 0, $backupType]
            ]);
        $this->backupMock->expects($this->once())
            ->method('load')
            ->with($backupId)
            ->willReturnSelf();

        $this->context = $this->objectManagerHelper->getObject(
            'Magento\Backend\App\Action\Context',
            [
                'request' => $this->requestMock,
                'messageManager' => $this->messageManagerMock,
                'resultFactory' => $this->resultFactoryMock
            ]
        );
        $this->downloadAction = $this->objectManagerHelper->getObject(
            'Magento\Support\Controller\Adminhtml\Backup\Download',
            [
                'context' => $this->context,
                'shellHelper' => $this->shellHelperMock,
                'backupFactory' => $this->backupFactoryMock,
                'fileFactory' => $this->fileFactoryMock,
                'file' => $this->fileMock
            ]
        );
    }

    /**
     * @return void
     */
    public function testExecute()
    {
        $filePath = 'some_path';
        $backupName = 'someName';

        $this->backupMock->expects($this->once())
            ->method('getItems')
            ->willReturn([
                $this->getAbstractItem($backupName),
                $this->getAbstractItem($backupName, 1),
            ]);
        $this->backupFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->backupMock);

        $this->shellHelperMock->expects($this->once())
            ->method('getFilePath')
            ->with($backupName)
            ->willReturn($filePath);
        $this->fileMock->expects($this->once())
            ->method('fileExists')
            ->with($filePath)
            ->willReturn(true);

        $this->fileFactoryMock->expects($this->once())
            ->method('create')
            ->with($backupName, ['value' => $filePath, 'type'  => 'filename']);

        $this->downloadAction->execute();
    }

    /**
     * @return void
     */
    public function testExecuteWithoutItems()
    {
        $this->backupMock->expects($this->once())
            ->method('getItems')
            ->willReturn([]);
        $this->backupFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->backupMock);

        $this->shellHelperMock->expects($this->never())
            ->method('getFilePath');
        $this->fileMock->expects($this->once())
            ->method('fileExists')
            ->with(null)
            ->willReturn(false);

        $this->messageManagerMock->expects($this->once())
            ->method('addError')
            ->with(__('File does not exist'))
            ->willReturnSelf();

        /** @var \Magento\Backend\Model\View\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject $redirectMock */
        $redirectMock = $this->getMock('Magento\Backend\Model\View\Result\Redirect', [], [], '', false);
        $redirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/index')
            ->willReturnSelf();
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)
            ->willReturn($redirectMock);

        $this->assertSame($redirectMock, $this->downloadAction->execute());
    }

    /**
     * @param string $backupName
     * @param int $type
     * @return \Magento\Support\Model\Backup\AbstractItem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAbstractItem($backupName, $type = 0)
    {
        /** @var \Magento\Support\Model\Backup\AbstractItem|\PHPUnit_Framework_MockObject_MockObject $item */
        $item = $this->getMockBuilder('Magento\Support\Model\Backup\AbstractItem')
            ->setMethods(['getType', 'getName'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $item->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $item->expects($this->any())
            ->method('getName')
            ->willReturn($backupName);

        return $item;
    }
}
