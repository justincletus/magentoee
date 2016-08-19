<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ScheduledImportExport\Test\Unit\Controller\Adminhtml\Scheduled\Operation;

use Magento\Framework\Controller\ResultFactory;

class CronTest extends \PHPUnit_Framework_TestCase
{
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
    }

    public function testCronActionFrontendAreaIsSetToDesignBeforeProcessOperation()
    {
        $designTheme = 'Magento/blank';

        $observer = $this->getMock(
            'Magento\ScheduledImportExport\Model\Observer',
            ['processScheduledOperation'],
            [],
            '',
            false
        );

        $theme = $this->getMock('Magento\Theme\Model\Theme', [], [], '', false);

        $design = $this->getMock(
            'Magento\Theme\Model\View\Design\Proxy',
            ['getArea', 'getDesignTheme', 'getConfigurationDesignTheme', 'setDesignTheme'],
            [],
            '',
            false
        );
        $design->expects($this->once())->method('getArea')
            ->willReturn('adminhtml');
        $design->expects($this->once())->method('getDesignTheme')
            ->willReturn($theme);
        $design->expects($this->once())->method('getConfigurationDesignTheme')
            ->with($this->equalTo(\Magento\Framework\App\Area::AREA_FRONTEND))
            ->willReturn($designTheme);

        $design->expects($this->at(3))->method('setDesignTheme')
            ->with($this->equalTo($designTheme), $this->equalTo(\Magento\Framework\App\Area::AREA_FRONTEND));
        $design->expects($this->at(4))->method('setDesignTheme')
            ->with($this->equalTo($theme), $this->equalTo('adminhtml'));

        $request = $this->getMock('Magento\Framework\App\Console\Request', ['getParam'], [], '', false);
        $request->expects($this->once())->method('getParam')
            ->with($this->equalTo('operation'))
            ->willReturn('2');

        $objectManagerMock = $this->getMock(
            'Magento\Framework\ObjectManager\ObjectManager',
            ['get'],
            [],
            '',
            false
        );
        $objectManagerMock->expects($this->at(0))->method('get')
            ->with($this->equalTo('Magento\Framework\View\DesignInterface'))
            ->willReturn($design);
        $objectManagerMock->expects($this->at(1))->method('get')
            ->with($this->equalTo('Magento\ScheduledImportExport\Model\Observer'))
            ->willReturn($observer);

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $context = $objectManager->getObject(
            'Magento\Backend\App\Action\Context',
            [
                'request' => $request,
                'objectManager' => $objectManagerMock,
                'resultFactory' => $this->resultFactoryMock
            ]
        );

        /** @var \Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation $instance */
        $instance = $objectManager->getObject(
            'Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation\Cron',
            ['context' => $context]
        );

        $this->assertSame($this->resultRedirectMock, $instance->execute());
    }
}
