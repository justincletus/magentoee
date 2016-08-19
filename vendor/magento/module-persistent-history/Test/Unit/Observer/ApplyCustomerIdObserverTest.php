<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PersistentHistory\Test\Unit\Observer;

class ApplyCustomerIdObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $historyHelperMoc;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $persistentHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionHelperMock;

    /**
     * @var \Magento\PersistentHistory\Observer\ApplyCustomerIdObserver
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->historyHelperMoc = $this->getMock('\Magento\PersistentHistory\Helper\Data', [], [], '', false);
        $this->sessionHelperMock = $this->getMock('\Magento\Persistent\Helper\Session', [], [], '', false);
        $this->persistentHelperMock = $this->getMock(
            '\Magento\Persistent\Helper\Data',
            ['isCompareProductsPersist', 'canProcess', '__wakeup'],
            [],
            '',
            false
        );

        $this->subject = $objectManager->getObject(
            'Magento\PersistentHistory\Observer\ApplyCustomerIdObserver',
            [
                'ePersistentData' => $this->historyHelperMoc,
                'persistentSession' => $this->sessionHelperMock,
                'mPersistentData' => $this->persistentHelperMock
            ]
        );
    }

    public function testApplyPersistentCustomerIdIfPersistentDataCantProcess()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->persistentHelperMock->expects($this->once())
            ->method('canProcess')
            ->with($observerMock)
            ->will($this->returnValue(false));
        $this->subject->execute($observerMock);
    }

    public function testApplyPersistentCustomerIdIfCannotCompareProduct()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->persistentHelperMock->expects($this->once())
            ->method('canProcess')
            ->with($observerMock)
            ->will($this->returnValue(true));
        $this->historyHelperMoc->expects($this->once())
            ->method('isCompareProductsPersist')
            ->will($this->returnValue(false));
        $this->subject->execute($observerMock);
    }

    public function testApplyPersistentCustomerIdSuccess()
    {
        $customerId = 1;
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->persistentHelperMock->expects($this->once())
            ->method('canProcess')
            ->with($observerMock)
            ->will($this->returnValue(true));
        $this->historyHelperMoc->expects($this->once())
            ->method('isCompareProductsPersist')
            ->will($this->returnValue(true));

        $actionMock = $this->getMock(
            'Magento\Framework\App\Test\Unit\Action\Stub\ActionStub',
            ['setCustomerId'],
            [],
            '',
            false
        );
        $actionMock->expects($this->once())->method('setCustomerId')->with($customerId)->will($this->returnSelf());

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getControllerAction'], [], '', false);
        $eventMock->expects($this->once())
            ->method('getControllerAction')
            ->will($this->returnValue($actionMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $sessionMock = $this->getMock(
            '\Magento\Persistent\Model\Session',
            ['getCustomerId', '__wakeup'],
            [],
            '',
            false
        );
        $sessionMock->expects($this->once())->method('getCustomerId')->will($this->returnValue($customerId));
        $this->sessionHelperMock->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($sessionMock));

        $this->subject->execute($observerMock);
    }
}
