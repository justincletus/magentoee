<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Observer\Backend;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class CmsPageDeleteAfterObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\VersionsCms\Model\ResourceModel\Increment|MockObject
     */
    protected $cmsIncrementMock;

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
     * @var \Magento\VersionsCms\Observer\Backend\CmsPageDeleteAfterObserver
     */
    protected $observer;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->cmsIncrementMock = $this->getMock(
            'Magento\VersionsCms\Model\ResourceModel\Increment',
            [],
            [],
            '',
            false
        );
        $this->eventObserverMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $this->pageMock = $this->getMockBuilder('Magento\Cms\Model\Page')
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $this->observer = $this->objectManagerHelper->getObject(
            'Magento\VersionsCms\Observer\Backend\CmsPageDeleteAfterObserver',
            [
                'cmsIncrement' => $this->cmsIncrementMock
            ]
        );
    }

    /**
     * @return void
     */
    public function testCmsPageDeleteAfter()
    {
        $pageId = 1;
        $this->initEventMock();

        $this->pageMock->expects($this->once())
            ->method('getId')
            ->willReturn($pageId);

        $this->cmsIncrementMock->expects($this->once())
            ->method('cleanIncrementRecord')
            ->with(
                \Magento\VersionsCms\Model\Increment::TYPE_PAGE,
                $pageId,
                \Magento\VersionsCms\Model\Increment::LEVEL_VERSION
            )
            ->willReturnSelf();

        $this->assertSame(
            $this->observer,
            $this->observer->execute($this->eventObserverMock)
        );
    }

    /**
     * @return void
     */
    protected function initEventMock()
    {
        /** @var \Magento\Framework\Event|MockObject $eventMock */
        $eventMock = $this->getMock('Magento\Framework\Event', ['getObject'], [], '', false);
        $eventMock->expects($this->once())
            ->method('getObject')
            ->willReturn($this->pageMock);
        $this->eventObserverMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);
    }
}
