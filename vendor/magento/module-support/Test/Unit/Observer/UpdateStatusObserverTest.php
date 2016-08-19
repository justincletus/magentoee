<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Observer;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class UpdateStatusObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Support\Observer\UpdateStatusObserver
     */
    protected $observer;

    /**
     * @var \Magento\Support\Model\ResourceModel\Backup\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionMock;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->collectionMock = $this->getMockBuilder('Magento\Support\Model\ResourceModel\Backup\Collection')
            ->disableOriginalConstructor()
            ->setMethods(['addFieldToFilter', 'getIterator'])
            ->getMock();

        $this->observer = $this->objectManagerHelper->getObject(
            'Magento\Support\Observer\UpdateStatusObserver',
            ['collection' => $this->collectionMock]
        );
    }

    /**
     * @return void
     */
    public function testUpdateStatus()
    {
        /** @var \Magento\Support\Model\Backup\AbstractItem|\PHPUnit_Framework_MockObject_MockObject $item */
        $item = $this->getMockBuilder('Magento\Support\Model\Backup\AbstractItem')
            ->disableOriginalConstructor()
            ->setMethods(['updateStatus'])
            ->getMockForAbstractClass();
        $item->expects($this->once())
            ->method('updateStatus');
        $itemCollection = [$item];

        /** @var \Magento\Support\Model\Backup|\PHPUnit_Framework_MockObject_MockObject $backup */
        $backup = $this->getMock('Magento\Support\Model\Backup', [], [], '', false);
        $backup->expects($this->once())
            ->method('updateStatus');
        $backup->expects($this->once())
            ->method('getItems')
            ->willReturn($itemCollection);
        $backupCollection = [$backup];

        $this->collectionMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with('status', ['neq' => \Magento\Support\Model\Backup::STATUS_COMPLETE])
            ->willReturnSelf();
        $this->collectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator($backupCollection));

        $observerMock = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertSame($this->observer, $this->observer->execute($observerMock));
    }
}
