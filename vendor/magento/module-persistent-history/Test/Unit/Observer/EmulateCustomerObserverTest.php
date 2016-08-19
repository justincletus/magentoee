<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PersistentHistory\Test\Unit\Observer;

class EmulateCustomerObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ePersistentDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mPersistentDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $persistentSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $emulatorMock;

    /**
     * @var \Magento\PersistentHistory\Observer\EmulateCustomerObserver
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->ePersistentDataMock = $this->getMock(
            '\Magento\PersistentHistory\Helper\Data',
            ['isCustomerAndSegmentsPersist'],
            [],
            '',
            false
        );
        $this->persistentSessionMock = $this->getMock('\Magento\Persistent\Helper\Session', [], [], '', false);

        $this->customerSessionMock = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        $this->emulatorMock = $this->getMock(
            '\Magento\PersistentHistory\Model\CustomerEmulator',
            [],
            [],
            '',
            false
        );
        $this->mPersistentDataMock = $this->getMock(
            '\Magento\Persistent\Helper\Data',
            ['canProcess', '__wakeup'],
            [],
            '',
            false
        );

        $this->subject = $objectManager->getObject(
            'Magento\PersistentHistory\Observer\EmulateCustomerObserver',
            [
                'ePersistentData' => $this->ePersistentDataMock,
                'persistentSession' => $this->persistentSessionMock,
                'mPersistentData' => $this->mPersistentDataMock,
                'customerSession' => $this->customerSessionMock,
                'customerEmulator' => $this->emulatorMock
            ]
        );
    }

    public function testSetPersistentDataIfDataCannotBeProcessed()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->mPersistentDataMock->expects($this->once())
            ->method('canProcess')
            ->with($observerMock)
            ->will($this->returnValue(false));
        $this->subject->execute($observerMock);
    }

    public function testSetPersistentDataIfCustomerIsNotPersistent()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->mPersistentDataMock->expects($this->once())
            ->method('canProcess')
            ->with($observerMock)
            ->will($this->returnValue(true));
        $this->ePersistentDataMock->expects($this->once())
            ->method('isCustomerAndSegmentsPersist')
            ->will($this->returnValue(false));
        $this->subject->execute($observerMock);
    }

    public function testApplyPersistentDataIfSessionNotPersistent()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->mPersistentDataMock->expects($this->once())
            ->method('canProcess')
            ->with($observerMock)
            ->will($this->returnValue(true));
        $this->ePersistentDataMock->expects($this->once())
            ->method('isCustomerAndSegmentsPersist')
            ->will($this->returnValue(true));
        $this->persistentSessionMock->expects($this->once())->method('isPersistent')->will($this->returnValue(false));
        $this->subject->execute($observerMock);
    }

    public function testApplyPersistentDataIfUserLoggedIn()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->mPersistentDataMock->expects($this->once())
            ->method('canProcess')
            ->with($observerMock)
            ->will($this->returnValue(true));
        $this->ePersistentDataMock->expects($this->once())
            ->method('isCustomerAndSegmentsPersist')
            ->will($this->returnValue(true));
        $this->persistentSessionMock->expects($this->once())->method('isPersistent')->will($this->returnValue(true));
        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue(true));
        $this->subject->execute($observerMock);
    }
    public function testApplyPersistentDataSuccess()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->mPersistentDataMock->expects($this->once())
            ->method('canProcess')
            ->with($observerMock)
            ->will($this->returnValue(true));
        $this->ePersistentDataMock->expects($this->once())
            ->method('isCustomerAndSegmentsPersist')
            ->will($this->returnValue(true));
        $this->persistentSessionMock->expects($this->once())->method('isPersistent')->will($this->returnValue(true));
        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue(false));
        $this->emulatorMock->expects($this->once())->method('emulate');
        $this->subject->execute($observerMock);
    }
}
