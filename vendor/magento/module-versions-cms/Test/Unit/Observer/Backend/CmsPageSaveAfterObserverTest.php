<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Observer\Backend;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CmsPageSaveAfterObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\VersionsCms\Model\Page\VersionFactory|MockObject
     */
    protected $pageVersionFactoryMock;

    /**
     * @var \Magento\VersionsCms\Helper\Hierarchy|MockObject
     */
    protected $cmsHierarchyMock;

    /**
     * @var \Magento\Backend\Model\Auth\Session|MockObject
     */
    protected $backendAuthSessionMock;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\Node|MockObject
     */
    protected $hierarchyNodeMock;

    /**
     * @var \Magento\VersionsCms\Model\ResourceModel\Hierarchy\Node|MockObject
     */
    protected $hierarchyNodeResourceMock;

    /**
     * @var \Magento\Framework\Event\Observer|MockObject
     */
    protected $eventObserverMock;

    /**
     * @var \Magento\Cms\Model\Page|MockObject
     */
    protected $pageMock;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\VersionsCms\Observer\Backend\CmsPageSaveAfterObserver
     */
    protected $observer;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->cmsHierarchyMock = $this->getMock('Magento\VersionsCms\Helper\Hierarchy', [], [], '', false);
        $this->backendAuthSessionMock = $this->getMockBuilder('Magento\Backend\Model\Auth\Session')
            ->disableOriginalConstructor()
            ->setMethods(['getUser'])
            ->getMock();
        $this->hierarchyNodeMock = $this->getMock('Magento\VersionsCms\Model\Hierarchy\Node', [], [], '', false);
        $this->pageVersionFactoryMock = $this->getMock(
            'Magento\VersionsCms\Model\Page\VersionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->hierarchyNodeResourceMock = $this->getMock(
            'Magento\VersionsCms\Model\ResourceModel\Hierarchy\Node',
            [],
            [],
            '',
            false
        );
        $this->eventObserverMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $this->pageMock = $this->getMockBuilder('Magento\Cms\Model\Page')
            ->disableOriginalConstructor()
            ->setMethods([
                'getId', 'setIsNewPage', 'setWebsiteRoot', 'setIsActive', 'setNodesSortOrder', 'dataHasChangedFor',
                'setAppendToNodes', 'setPublishedRevisionId', 'getUnderVersionControl', 'getNodesData', 'getIsNewPage',
                'getAppendToNodes', 'getNodesSortOrder', 'getData', 'getTitle'
            ])
            ->getMock();

        $this->observer = $this->objectManagerHelper->getObject(
            'Magento\VersionsCms\Observer\Backend\CmsPageSaveAfterObserver',
            [
                'cmsHierarchy' => $this->cmsHierarchyMock,
                'backendAuthSession' => $this->backendAuthSessionMock,
                'hierarchyNode' => $this->hierarchyNodeMock,
                'pageVersionFactory' => $this->pageVersionFactoryMock,
                'hierarchyNodeResource' => $this->hierarchyNodeResourceMock,
            ]
        );
    }

    /**
     * @return void
     */
    public function testCmsPageSaveAfter()
    {
        /** @var \Magento\Framework\Event|MockObject $eventMock */
        $eventMock = $this->getMock('Magento\Framework\Event', ['getObject'], [], '', false);
        $eventMock->expects($this->once())
            ->method('getObject')
            ->willReturn($this->pageMock);
        $this->eventObserverMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $appendToNodes = ['node 1', 'node2'];

        $this->createNewInitialVersionRevisionTest();

        $this->cmsHierarchyMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);
        $this->pageMock->expects($this->once())
            ->method('dataHasChangedFor')
            ->with('identifier')
            ->willReturn(true);
        $this->hierarchyNodeMock->expects($this->once())
            ->method('updateRewriteUrls')
            ->with($this->pageMock)
            ->willReturnSelf();
        $this->pageMock->expects($this->once())
            ->method('getAppendToNodes')
            ->willReturn($appendToNodes);
        $this->hierarchyNodeMock->expects($this->once())
            ->method('appendPageToNodes')
            ->with($this->pageMock, $appendToNodes)
            ->willReturnSelf();
        $this->pageMock->expects($this->once())
            ->method('getNodesSortOrder')
            ->willReturn([1 => 'node 1']);
        $this->hierarchyNodeResourceMock->expects($this->once())
            ->method('updateSortOrder')
            ->with(1, 'node 1')
            ->willReturnSelf();

        $this->assertSame(
            $this->observer,
            $this->observer->execute($this->eventObserverMock)
        );
    }

    /**
     * @return void
     */
    protected function createNewInitialVersionRevisionTest()
    {
        $pageTitle = 'Page title';
        $pageId = 1;
        $userId = 2;

        $this->pageMock->expects($this->once())
            ->method('getIsNewPage')
            ->willReturn(true);
        $this->pageMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($pageTitle);
        $this->pageMock->expects($this->once())
            ->method('getId')
            ->willReturn($pageId);
        $this->pageMock->expects($this->once())
            ->method('getData')
            ->willReturn(['copied_from_original' => false]);
        $this->pageMock->expects($this->once())
            ->method('getUnderVersionControl')
            ->willReturn(true);

        /** @var \Magento\User\Model\User|MockObject $userMock */
        $userMock = $this->getMock('Magento\User\Model\User', [], [], '', false);
        $userMock->expects($this->once())
            ->method('getId')
            ->willReturn($userId);
        $this->backendAuthSessionMock->expects($this->once())
            ->method('getUser')
            ->willReturn($userMock);

        /** @var \Magento\VersionsCms\Model\Page\Revision|MockObject $revisionMock */
        $revisionMock = $this->getMock('Magento\VersionsCms\Model\Page\Revision', [], [], '', false);
        $revisionMock->expects($this->once())
            ->method('publish')
            ->willReturnSelf();

        /** @var \Magento\VersionsCms\Model\Page\Version|MockObject $versionMock */
        $versionMock = $this->getMockBuilder('Magento\VersionsCms\Model\Page\Version')
            ->disableOriginalConstructor()
            ->setMethods([
                'setLabel', 'setAccessLevel', 'setPageId', 'getLastRevision',
                'setUserId', 'setInitialRevisionData', 'save'
            ])
            ->getMock();
        $versionMock->expects($this->once())
            ->method('setLabel')
            ->with($pageTitle)
            ->willReturnSelf();
        $versionMock->expects($this->once())
            ->method('setAccessLevel')
            ->with(\Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PUBLIC)
            ->willReturnSelf();
        $versionMock->expects($this->once())
            ->method('setPageId')
            ->with($pageId)
            ->willReturnSelf();
        $versionMock->expects($this->once())
            ->method('setUserId')
            ->with($userId)
            ->willReturnSelf();
        $versionMock->expects($this->once())
            ->method('setInitialRevisionData')
            ->with(['copied_from_original' => true])
            ->willReturnSelf();
        $versionMock->expects($this->once())
            ->method('save');
        $versionMock->expects($this->once())
            ->method('getLastRevision')
            ->willReturn($revisionMock);
        $this->pageVersionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($versionMock);
    }

    /**
     * @return void
     */
    public function testCmsPageSaveAfterWithCmsHierarchyDisabled()
    {
        /** @var \Magento\Framework\Event|MockObject $eventMock */
        $eventMock = $this->getMock('Magento\Framework\Event', ['getObject'], [], '', false);
        $eventMock->expects($this->once())
            ->method('getObject')
            ->willReturn($this->pageMock);
        $this->eventObserverMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);


        $this->cmsHierarchyMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->assertSame(
            $this->observer,
            $this->observer->execute($this->eventObserverMock)
        );
    }
}
