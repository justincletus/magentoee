<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Controller\Adminhtml\Cms\Hierarchy\Widget;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class ChooserTest
 */
class ChooserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\App\ViewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewMock;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\VersionsCms\Controller\Adminhtml\Cms\Hierarchy\Widget\Chooser
     */
    protected $chooser;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp()
    {
        $this->contextMock = $this->getMock(
            'Magento\Backend\App\Action\Context',
            ['getView', 'getRequest', 'getResponse'],
            [],
            '',
            false
        );
        $this->viewMock = $this->getMockForAbstractClass(
            'Magento\Framework\App\ViewInterface',
            [],
            '',
            false,
            true,
            true,
            ['getLayout']
        );
        $this->requestMock = $this->getMockForAbstractClass(
            'Magento\Framework\App\RequestInterface',
            [],
            '',
            false,
            true,
            true,
            ['getParam']
        );
        $this->responseMock = $this->getMockForAbstractClass(
            'Magento\Framework\App\ResponseInterface',
            [],
            '',
            false,
            true,
            true,
            ['setBody']
        );

        $this->objectManager = new ObjectManager($this);

        $this->contextMock->expects($this->once())
            ->method('getView')
            ->willReturn($this->viewMock);
        $this->contextMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->requestMock);
        $this->contextMock->expects($this->once())
            ->method('getResponse')
            ->willReturn($this->responseMock);

        $this->chooser = $this->objectManager->getObject(
            'Magento\VersionsCms\Controller\Adminhtml\Cms\Hierarchy\Widget\Chooser',
            [
                'context' => $this->contextMock
            ]
        );
    }

    /**
     * Run test execute method
     *
     * @return void
     */
    public function testExecute()
    {
        $uniqId = 78946;
        $scope = 'scope-value';
        $scopeId = 744112;
        $html = 'test-html';

        $layoutMock = $this->getMockForAbstractClass(
            'Magento\Framework\View\LayoutInterface',
            [],
            '',
            false,
            true,
            true,
            ['createBlock']
        );
        $chooserMock = $this->getMock(
            'Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Widget\Chooser',
            ['setScope', 'setScopeId', 'toHtml'],
            [],
            '',
            false
        );

        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['uniq_id', null, $uniqId],
                    ['scope', null, $scope],
                    ['scope_id', null, $scopeId]
                ]
            );

        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->willReturn($layoutMock);
        $layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(
                'Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Widget\Chooser',
                '',
                ['data' => ['id' => $uniqId]]
            )
            ->willReturn($chooserMock);
        $chooserMock->expects($this->once())
            ->method('setScope')
            ->with($scope)
            ->willReturnSelf();
        $chooserMock->expects($this->once())
            ->method('setScopeId')
            ->with($scopeId)
            ->willReturnSelf();
        $chooserMock->expects($this->once())
            ->method('toHtml')
            ->willReturn($html);
        $this->responseMock->expects($this->once())
            ->method('setBody')
            ->with($html);

        $this->chooser->execute();
    }
}
