<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Observer;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\VersionsCms\Model\Hierarchy\Node as NodeMock;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class AffectCmsPageRenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\VersionsCms\Helper\Hierarchy|MockObject
     */
    protected $cmsHierarchyMock;

    /**
     * @var \Magento\Framework\Registry|MockObject
     */
    protected $coreRegistryMock;

    /**
     * @var \Magento\Framework\App\ViewInterface|MockObject
     */
    protected $viewMock;

    /**
     * @var \Magento\Framework\Event\Observer|MockObject
     */
    protected $eventObserver;

    /**
     * @var \Magento\Framework\View\Layout\ProcessorInterface|MockObject
     */
    protected $updateMock;

    /**
     * @var \Magento\VersionsCms\Observer\AffectCmsPageRender
     */
    protected $observer;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->cmsHierarchyMock = $this->getMock('Magento\VersionsCms\Helper\Hierarchy', [], [], '', false);
        $this->coreRegistryMock = $this->getMock('Magento\Framework\Registry', [], [], '', false);
        $this->viewMock = $this->getMock('Magento\Framework\App\ViewInterface', [], [], '', false);
        $this->eventObserver = $this->getMock('Magento\Framework\Event\Observer', ['getPage'], [], '', false);
        $this->updateMock = $this->getMock('Magento\Framework\View\Layout\ProcessorInterface');

        /** @var \Magento\Framework\View\LayoutInterface|MockObject $layoutMock */
        $layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface');
        $layoutMock->expects($this->any())
            ->method('getUpdate')
            ->willReturn($this->updateMock);
        $this->viewMock->expects($this->any())
            ->method('getLayout')
            ->willReturn($layoutMock);

        $this->observer = $this->objectManagerHelper->getObject(
            'Magento\VersionsCms\Observer\AffectCmsPageRender',
            [
                'cmsHierarchy' => $this->cmsHierarchyMock,
                'coreRegistry' => $this->coreRegistryMock,
                'view' => $this->viewMock
            ]
        );
    }

    /**
     * @param NodeMock|null $node
     * @param bool $hierarchyEnabled
     * @return void
     * @dataProvider invokeWhenHierarchyDisabledOrNodeAbsentDataProvider
     */
    public function testInvokeWhenHierarchyDisabledOrNodeAbsent($node, $hierarchyEnabled)
    {
        $this->coreRegistryMock->expects($this->once())
            ->method('registry')
            ->with('current_cms_hierarchy_node')
            ->willReturn($node);

        $this->cmsHierarchyMock->expects($this->any())
            ->method('isEnabled')
            ->willReturn($hierarchyEnabled);

        $this->updateMock->expects($this->never())
            ->method('getHandles');
        $this->updateMock->expects($this->never())
            ->method('addHandle');

        $this->assertSame($this->observer, $this->observer->execute($this->eventObserver));
    }

    /**
     * @return array
     */
    public function invokeWhenHierarchyDisabledOrNodeAbsentDataProvider()
    {
        return [
            ['node' => null, 'hierarchyEnabled' => true],
            ['node' => null, 'hierarchyEnabled' => false],
            ['node' => $this->getNodeMock(), 'hierarchyEnabled' => false]
        ];
    }

    /**
     * @return void
     */
    public function testInvokeWhenMenuLayoutEmpty()
    {
        $this->generalInvokeTest(null, '2columns-right', []);

        $this->updateMock->expects($this->never())
            ->method('addHandle');

        $this->assertSame($this->observer, $this->observer->execute($this->eventObserver));
    }

    /**
     * @return void
     */
    public function testInvokeWhenAllowedNonIntersectLoadedHandles()
    {
        $loadedHandles = ['default', 'cms_page'];
        $menuLayout = [
            'pageLayoutHandles' => ['2columns-left', '3columns'],
            'handle' => 'menu_left_column'
        ];

        $this->generalInvokeTest($menuLayout, '2columns-right', $loadedHandles);

        $this->updateMock->expects($this->never())
            ->method('addHandle');

        $this->assertSame($this->observer, $this->observer->execute($this->eventObserver));
    }

    /**
     * @return void
     */
    public function testInvoke()
    {
        $loadedHandles = ['default', 'cms_page'];
        $menuLayout = [
            'pageLayoutHandles' => ['2columns-left', '3columns'],
            'handle' => 'menu_left_column'
        ];

        $this->generalInvokeTest($menuLayout, '2columns-left', $loadedHandles);

        $this->updateMock->expects($this->once())
            ->method('addHandle')
            ->with($menuLayout['handle']);

        $this->assertSame($this->observer, $this->observer->execute($this->eventObserver));
    }

    /**
     * @param array|null $menuLayout
     * @param string $pageLayout
     * @param array $loadedHandles
     * @return void
     */
    protected function generalInvokeTest($menuLayout, $pageLayout, $loadedHandles)
    {
        $nodeMock = $this->getNodeMock();
        $nodeMock->expects($this->once())
            ->method('getMenuLayout')
            ->willReturn($menuLayout);

        /** @var \Magento\CMS\Model\Page|MockObject $pageMock */
        $pageMock = $this->getMock('Magento\CMS\Model\Page', [], [], '', false);
        $pageMock->expects($this->once())
            ->method('getPageLayout')
            ->willReturn($pageLayout);

        $this->coreRegistryMock->expects($this->atLeastOnce())
            ->method('registry')
            ->with('current_cms_hierarchy_node')
            ->willReturn($nodeMock);
        $this->cmsHierarchyMock->expects($this->any())
            ->method('isEnabled')
            ->willReturn(true);
        $this->updateMock->expects($this->once())
            ->method('getHandles')
            ->willReturn($loadedHandles);
        $this->eventObserver->expects($this->once())
            ->method('getPage')
            ->willReturn($pageMock);
    }

    /**
     * @return NodeMock|MockObject
     */
    protected function getNodeMock()
    {
        return $this->getMock('Magento\VersionsCms\Model\Hierarchy\Node', [], [], '', false);
    }
}
