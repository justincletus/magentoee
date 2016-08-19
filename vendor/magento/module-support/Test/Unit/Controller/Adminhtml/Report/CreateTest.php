<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Controller\Adminhtml\Report;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\Controller\ResultFactory;

class CreateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Backend\Model\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageMock;

    /**
     * @var \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $titleMock;

    /**
     * @var \Magento\Support\Controller\Adminhtml\Report\Create
     */
    protected $createAction;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->messageManagerMock = $this->getMock('Magento\Framework\Message\ManagerInterface');
        $this->resultPageMock = $this->getMock('Magento\Backend\Model\View\Result\Page', [], [], '', false);
        $this->resultFactoryMock = $this->getMock('Magento\Framework\Controller\ResultFactory', [], [], '', false);
        $this->configMock = $this->getMock('Magento\Framework\View\Page\Config', [], [], '', false);
        $this->titleMock = $this->getMock('Magento\Framework\View\Page\Title', [], [], '', false);

        $this->contextMock = $this->getMock('Magento\Backend\App\Action\Context', [], [], '', false);
        $this->contextMock->expects($this->any())
            ->method('getMessageManager')
            ->willReturn($this->messageManagerMock);
        $this->contextMock->expects($this->any())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);

        $this->createAction = $this->objectManagerHelper->getObject(
            'Magento\Support\Controller\Adminhtml\Report\Create',
            [
                'context' => $this->contextMock
            ]
        );
    }

    /**
     * @return void
     */
    public function testExecuteMainFlow()
    {
        $message = 'After you make your selections, click the "Create" button.'
            . ' Then stand by while the System Report is generated. This may take a few minutes.'
            . ' You will receive a notification once this step is completed.';
        $this->messageManagerMock->expects($this->once())
            ->method('addWarning')
            ->with(__($message))
            ->willReturnSelf();

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_PAGE)
            ->willReturn($this->resultPageMock);

        $this->resultPageMock->expects($this->once())
            ->method('setActiveMenu')
            ->with('Magento_Support::support_report')
            ->willReturnSelf();

        $this->resultPageMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->configMock);
        $this->configMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($this->titleMock);
        $this->titleMock->expects($this->once())
            ->method('prepend')
            ->with(__('Create System Report'));

        $this->assertSame($this->resultPageMock, $this->createAction->execute());
    }
}
