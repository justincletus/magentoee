<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Controller\Adminhtml\Report;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\Controller\ResultFactory;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $titleMock;

    /**
     * @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Backend\Model\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageMock;

    /**
     * @var ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Support\Controller\Adminhtml\Report\Index
     */
    protected $indexAction;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->titleMock = $this->getMock('Magento\Framework\View\Page\Title', [], [], '', false);
        $this->configMock = $this->getMock('Magento\Framework\View\Page\Config', [], [], '', false);
        $this->resultPageMock = $this->getMock('Magento\Backend\Model\View\Result\Page', [], [], '', false);
        $this->resultFactoryMock = $this->getMock('Magento\Framework\Controller\ResultFactory', [], [], '', false);

        $this->contextMock = $this->getMock('Magento\Backend\App\Action\Context', [], [], '', false);
        $this->contextMock->expects($this->any())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);

        $this->indexAction = $this->objectManagerHelper->getObject(
            'Magento\Support\Controller\Adminhtml\Report\Index',
            ['context' => $this->contextMock]
        );
    }

    /**
     * @return void
     */
    public function testExecute()
    {
        $this->titleMock->expects($this->once())
            ->method('prepend')
            ->with(__('System Reports'));

        $this->configMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($this->titleMock);

        $this->resultPageMock->expects($this->once())
            ->method('setActiveMenu')
            ->with('Magento_Support::support_report')
            ->willReturnSelf();
        $this->resultPageMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->configMock);

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_PAGE)
            ->willReturn($this->resultPageMock);

        $this->assertSame($this->resultPageMock, $this->indexAction->execute());
    }
}
