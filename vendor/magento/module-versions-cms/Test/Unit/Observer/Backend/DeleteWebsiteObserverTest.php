<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Observer\Backend;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class DeleteWebsiteObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\NodeFactory|MockObject
     */
    protected $hierarchyNodeFactoryMock;

    /**
     * @var \Magento\VersionsCms\Observer\Backend\CleanStoreFootprints|MockObject
     */
    protected $cleanStoreFootprintsMock;

    /**
     * @var \Magento\Framework\Event\Observer|MockObject
     */
    protected $eventObserverMock;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\VersionsCms\Observer\Backend\DeleteWebsiteObserver
     */
    protected $observer;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->hierarchyNodeFactoryMock = $this->getMock(
            'Magento\VersionsCms\Model\Hierarchy\NodeFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->cleanStoreFootprintsMock = $this->getMock(
            'Magento\VersionsCms\Observer\Backend\CleanStoreFootprints',
            [],
            [],
            '',
            false
        );
        $this->eventObserverMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);

        $this->observer = $this->objectManagerHelper->getObject(
            'Magento\VersionsCms\Observer\Backend\DeleteWebsiteObserver',
            [
                'hierarchyNodeFactory' => $this->hierarchyNodeFactoryMock,
                'cleanStoreFootprints' => $this->cleanStoreFootprintsMock,
            ]
        );
    }

    /**
     * @return void
     */
    public function testDeleteWebsite()
    {
        $websiteId = 1;
        $storeId = 2;

        /** @var \Magento\Store\Model\Website|MockObject $websiteMock */
        $websiteMock = $this->getMock('Magento\Store\Model\Website', ['getId', 'getStoreIds'], [], '', false);
        $websiteMock->expects($this->once())
            ->method('getId')
            ->willReturn($websiteId);
        $websiteMock->expects($this->once())
            ->method('getStoreIds')
            ->willReturn([$storeId]);

        $this->hierarchyNodeDeleteByScope($websiteId);

        /** @var \Magento\Framework\Event|MockObject $eventMock */
        $eventMock = $this->getMock('Magento\Framework\Event', ['getWebsite'], [], '', false);
        $eventMock->expects($this->once())
            ->method('getWebsite')
            ->willReturn($websiteMock);

        $this->eventObserverMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->cleanStoreFootprintsMock->expects($this->once())->method('clean')->with($storeId);

        $this->assertSame(
            $this->observer,
            $this->observer->execute($this->eventObserverMock)
        );
    }

    /**
     * @param int $id
     * @return void
     */
    protected function hierarchyNodeDeleteByScope($id)
    {
        /** @var \Magento\VersionsCms\Model\Hierarchy\Node|MockObject $hierarchyNode */
        $hierarchyNode = $this->getMock('Magento\VersionsCms\Model\Hierarchy\Node', [], [], '', false);
        $hierarchyNode->expects($this->any())
            ->method('deleteByScope')
            ->willReturnMap([
                [\Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_STORE, $id],
                [\Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_WEBSITE, $id]
            ]);
        $this->hierarchyNodeFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($hierarchyNode);
    }
}
