<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Controller\Adminhtml\Backup;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class LogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Support\Controller\Adminhtml\Backup\Log
     */
    protected $logAction;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->resultFactoryMock = $this->getMock('Magento\Framework\Controller\ResultFactory', [], [], '', false);

        $this->context = $this->objectManagerHelper->getObject(
            'Magento\Backend\App\Action\Context',
            ['resultFactory' => $this->resultFactoryMock]
        );
        $this->logAction = $this->objectManagerHelper->getObject(
            'Magento\Support\Controller\Adminhtml\Backup\Log',
            ['context' => $this->context]
        );
    }

    /**
     * @return void
     */
    public function testExecute()
    {
        /** @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject $title */
        $title = $this->getMock('Magento\Framework\View\Page\Title', [], [], '', false);
        $title->expects($this->once())
            ->method('prepend')
            ->with(__('Log Details'));

        /** @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject $pageConfig */
        $pageConfig = $this->getMock('Magento\Framework\View\Page\Config', [], [], '', false);
        $pageConfig->expects($this->once())
            ->method('getTitle')
            ->willReturn($title);

        /** @var \Magento\Backend\Model\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject $resultPage */
        $resultPage = $this->getMock('Magento\Backend\Model\View\Result\Page', [], [], '', false);
        $resultPage->expects($this->once())
            ->method('getConfig')
            ->willReturn($pageConfig);
        $resultPage->expects($this->once())
            ->method('setActiveMenu')
            ->with('Magento_Support::support_backup')
            ->willReturnSelf();
        $resultPage->expects($this->at(1))
            ->method('addBreadcrumb')
            ->with(__('Support'), __('Support'))
            ->willReturnSelf();
        $resultPage->expects($this->at(2))
            ->method('addBreadcrumb')
            ->with(__('Data Collector'), __('Data Collector'))
            ->willReturnSelf();
        $resultPage->expects($this->at(4))
            ->method('addBreadcrumb')
            ->with(__('Log Details'), __('Log Details'))
            ->willReturnSelf();

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE)
            ->willReturn($resultPage);

        $this->assertSame($resultPage, $this->logAction->execute());
    }
}
