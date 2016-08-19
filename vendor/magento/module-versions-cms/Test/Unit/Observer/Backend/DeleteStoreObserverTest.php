<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Observer\Backend;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class DeleteStoreObserverTest extends \PHPUnit_Framework_TestCase
{
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
     * @var \Magento\VersionsCms\Observer\Backend\DeleteStoreObserver
     */
    protected $observer;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->cleanStoreFootprintsMock = $this->getMock(
            'Magento\VersionsCms\Observer\Backend\CleanStoreFootprints',
            [],
            [],
            '',
            false
        );
        $this->eventObserverMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);

        $this->observer = $this->objectManagerHelper->getObject(
            'Magento\VersionsCms\Observer\Backend\DeleteStoreObserver',
            [
                'cleanStoreFootprints' => $this->cleanStoreFootprintsMock,
            ]
        );
    }

    /**
     * @return void
     */
    public function testDeleteStore()
    {
        $storeId = 2;

        /** @var \Magento\Store\Model\Store|MockObject $storeMock */
        $storeMock = $this->getMock('Magento\Store\Model\Store', ['getId'], [], '', false);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);

        /** @var \Magento\Framework\Event|MockObject $eventMock */
        $eventMock = $this->getMock('Magento\Framework\Event', ['getStore'], [], '', false);
        $eventMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->eventObserverMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->cleanStoreFootprintsMock->expects($this->once())->method('clean')->with($storeId);

        $this->observer->execute($this->eventObserverMock);
    }
}
