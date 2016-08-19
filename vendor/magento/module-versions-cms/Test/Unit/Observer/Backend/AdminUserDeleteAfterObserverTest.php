<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Observer\Backend;

use Magento\Framework\DB\Select;
use Magento\Framework\Event\Observer;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\VersionsCms\Model\Page\Version;
use Magento\VersionsCms\Model\Page\VersionFactory;
use Magento\VersionsCms\Model\ResourceModel\Page\Version\CollectionFactory;
use Magento\VersionsCms\Observer\Backend\AdminUserDeleteAfterObserver;
use Magento\VersionsCms\Observer\Backend\RemoveVersionCallback;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class AdminUserDeleteAfterObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var VersionFactory|MockObject
     */
    protected $pageVersionFactoryMock;

    /**
     * @var CollectionFactory|MockObject
     */
    protected $versionCollectionFactoryMock;

    /**
     * @var Iterator|MockObject
     */
    protected $resourceIteratorMock;

    /**
     * @var RemoveVersionCallback|MockObject
     */
    protected $removeVersionCallbackMock;

    /**
     * @var Observer|MockObject
     */
    protected $eventObserverMock;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var AdminUserDeleteAfterObserver
     */
    protected $observer;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->pageVersionFactoryMock = $this->getMock(
            'Magento\VersionsCms\Model\Page\VersionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->versionCollectionFactoryMock = $this->getMock(
            'Magento\VersionsCms\Model\ResourceModel\Page\Version\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->resourceIteratorMock = $this->getMock(
            'Magento\Framework\Model\ResourceModel\Iterator',
            [],
            [],
            '',
            false
        );
        $this->removeVersionCallbackMock = $this->getMock('Magento\VersionsCms\Observer\Backend\RemoveVersionCallback');
        $this->eventObserverMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $this->observer = $this->objectManagerHelper->getObject(
            'Magento\VersionsCms\Observer\Backend\AdminUserDeleteAfterObserver',
            [
                'pageVersionFactory' => $this->pageVersionFactoryMock,
                'versionCollectionFactory' => $this->versionCollectionFactoryMock,
                'resourceIterator' => $this->resourceIteratorMock,
                'removeVersionCallback' => $this->removeVersionCallbackMock,
            ]
        );
    }

    /**
     * @return void
     */
    public function testAdminUserDeleteAfter()
    {
        /** @var Select|MockObject $selectMock */
        $selectMock = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);
        $collectionVersionMock = $this->getMockBuilder(
            'Magento\VersionsCms\Model\ResourceModel\Page\Version\Collection'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $collectionVersionMock->expects($this->once())
            ->method('addAccessLevelFilter')
            ->with(Version::ACCESS_LEVEL_PRIVATE)
            ->willReturnSelf();
        $collectionVersionMock->expects($this->once())
            ->method('addUserIdFilter')
            ->willReturnSelf();
        $collectionVersionMock->expects($this->once())
            ->method('getSelect')
            ->willReturn($selectMock);
        $this->versionCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionVersionMock);

        /** @var Version|MockObject $versionMock */
        $versionMock = $this->getMock('Magento\VersionsCms\Model\Page\Version', [], [], '', false);
        $this->pageVersionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($versionMock);

        $this->resourceIteratorMock->expects($this->once())
            ->method('walk')
            ->with($selectMock, [[$this->removeVersionCallbackMock, 'execute']], ['version' => $versionMock])
            ->willReturnSelf();

        $this->observer->execute($this->eventObserverMock);
    }
}
