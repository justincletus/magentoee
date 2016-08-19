<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PersistentHistory\Test\Unit\Observer;

class ApplyBlockPersistentDataObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $persistentHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    /**
     * @var \Magento\PersistentHistory\Observer\ApplyBlockPersistentDataObserver
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->persistentHelperMock = $this->getMock('\Magento\PersistentHistory\Helper\Data', [], [], '', false);
        $this->observerMock = $this->getMock(
            '\Magento\Persistent\Observer\ApplyBlockPersistentDataObserver',
            [],
            [],
            '',
            false
        );

        $this->subject = $objectManager->getObject(
            'Magento\PersistentHistory\Observer\ApplyBlockPersistentDataObserver',
            [
                'ePersistentData' => $this->persistentHelperMock,
                'observer' => $this->observerMock,
            ]
        );
    }

    public function testApplyBlockPersistentData()
    {
        $configFilePath = 'file/path';
        $eventObserverMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);

        $eventMock = $this->getMock('\Magento\Framework\Event', ['setConfigFilePath'], [], '', false);
        $eventMock->expects($this->once())
            ->method('setConfigFilePath')
            ->with($configFilePath)
            ->will($this->returnSelf());

        $eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $this->persistentHelperMock->expects($this->once())
            ->method('getPersistentConfigFilePath')
            ->will($this->returnValue($configFilePath));

        $this->observerMock->expects($this->once())
            ->method('execute')
            ->with($eventObserverMock)
            ->will($this->returnSelf());

        $this->assertEquals($this->observerMock, $this->subject->execute($eventObserverMock));
    }
}
