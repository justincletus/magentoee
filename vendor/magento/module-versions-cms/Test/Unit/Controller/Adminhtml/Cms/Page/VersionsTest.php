<?php
/**
 * Tests Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Versions
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\VersionsCms\Test\Unit\Controller\Adminhtml\Cms\Page;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class VersionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageMock;

    /**
     * @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageConfigMock;

    /**
     * @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageTitleMock;

    public function testExecute()
    {
        $viewMock = $this->basicMock('\Magento\Framework\App\ViewInterface');
        $pageLoaderMock = $this->basicMock('\Magento\VersionsCms\Model\PageLoader');
        // Context Mock
        $contextMock = $this->basicMock('\Magento\Backend\App\Action\Context');

        $this->resultPageMock = $this->getMockBuilder('Magento\Framework\View\Result\Page')
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageConfigMock = $this->getMockBuilder('Magento\Framework\View\Page\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageTitleMock = $this->getMockBuilder('Magento\Framework\View\Page\Title')
            ->disableOriginalConstructor()
            ->getMock();

        $this->basicStub($contextMock, 'getView')->willReturn($viewMock);
        $this->basicStub($contextMock, 'getRequest')
            ->willReturn($this->basicMock('Magento\Framework\App\RequestInterface'));
        $this->basicStub($contextMock, 'getResponse')
            ->willReturn($this->basicMock('Magento\Framework\App\ResponseInterface'));
        $this->basicStub($contextMock, 'getTitle')
            ->willReturn($this->basicMock('Magento\Framework\App\Action\Title'));

        // SUT
        $mocks = [
            'context' => $contextMock,
            'pageLoader' => $pageLoaderMock,
        ];
        $objectManager = new ObjectManager($this);
        $model = $objectManager->getObject('Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Versions', $mocks);

        // Expectations and test
        $viewMock->expects($this->once())
            ->method('loadLayout');
        $viewMock->expects($this->once())
            ->method('renderLayout');
        $pageLoaderMock->expects($this->once())
            ->method('load');

        $viewMock->expects($this->any())
            ->method('getPage')
            ->willReturn($this->resultPageMock);
        $this->resultPageMock->expects($this->any())
            ->method('getConfig')
            ->willReturn($this->pageConfigMock);
        $this->pageConfigMock->expects($this->any())
            ->method('getTitle')
            ->willReturn($this->pageTitleMock);

        $model->execute();
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param string $method
     *
     * @return \PHPUnit_Framework_MockObject_Builder_InvocationMocker
     */
    private function basicStub($mock, $method)
    {
        return $mock->expects($this->any())
            ->method($method)
            ->withAnyParameters();
    }

    /**
     * @param string $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function basicMock($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
