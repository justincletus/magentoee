<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PersistentHistory\Test\Unit\Observer;

class ApplyPersistentDataObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $historyHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $persistentHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configFactoryMock;

    /**
     * @var \Magento\PersistentHistory\Observer\ApplyPersistentDataObserver
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->historyHelperMock = $this->getMock('\Magento\PersistentHistory\Helper\Data', [], [], '', false);
        $this->sessionHelperMock = $this->getMock('\Magento\Persistent\Helper\Session', [], [], '', false);
        $this->customerSessionMock = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        $this->configFactoryMock = $this->getMock(
            '\Magento\Persistent\Model\Persistent\ConfigFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->persistentHelperMock = $this->getMock(
            '\Magento\Persistent\Helper\Data',
            ['isCompareProductsPersist', 'canProcess', '__wakeup'],
            [],
            '',
            false
        );

        $this->subject = $objectManager->getObject(
            'Magento\PersistentHistory\Observer\ApplyPersistentDataObserver',
            [
                'ePersistentData' => $this->historyHelperMock,
                'persistentSession' => $this->sessionHelperMock,
                'mPersistentData' => $this->persistentHelperMock,
                'customerSession' => $this->customerSessionMock,
                'configFactory' => $this->configFactoryMock
            ]
        );
    }

    public function testApplyPersistentDataIfDataCantProcess()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->persistentHelperMock->expects($this->once())
            ->method('canProcess')
            ->with($observerMock)
            ->will($this->returnValue(false));
        $this->subject->execute($observerMock);
    }

    public function testApplyPersistentDataIfSessionNotPersistent()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->persistentHelperMock->expects($this->once())
            ->method('canProcess')
            ->with($observerMock)
            ->will($this->returnValue(true));
        $this->sessionHelperMock->expects($this->once())->method('isPersistent')->will($this->returnValue(false));
        $this->subject->execute($observerMock);
    }

    public function testApplyPersistentDataIfUserLoggedIn()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->persistentHelperMock->expects($this->once())
            ->method('canProcess')
            ->with($observerMock)
            ->will($this->returnValue(true));
        $this->sessionHelperMock->expects($this->once())->method('isPersistent')->will($this->returnValue(true));
        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue(true));
        $this->subject->execute($observerMock);
    }

    public function testApplyPersistentDataSuccess()
    {
        $configFilePath = 'file/path';
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->persistentHelperMock->expects($this->once())
            ->method('canProcess')
            ->with($observerMock)
            ->will($this->returnValue(true));
        $this->sessionHelperMock->expects($this->once())->method('isPersistent')->will($this->returnValue(true));
        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue(false));

        $configMock = $this->getMock('\Magento\Persistent\Model\Persistent\Config', [], [], '', false);
        $configMock->expects($this->once())
            ->method('setConfigFilePath')
            ->with($configFilePath)
            ->will($this->returnSelf());

        $this->historyHelperMock->expects($this->once())
            ->method('getPersistentConfigFilePath')
            ->will($this->returnValue($configFilePath));

        $this->configFactoryMock->expects($this->once())->method('create')->will($this->returnValue($configMock));
        $this->subject->execute($observerMock);
    }
}
