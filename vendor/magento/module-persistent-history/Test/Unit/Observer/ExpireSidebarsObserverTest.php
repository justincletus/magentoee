<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PersistentHistory\Test\Unit\Observer;

class ExpireSidebarsObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $historyHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $compareItemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $comparedFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewedFactoryMock;

    /**
     * @var \Magento\PersistentHistory\Observer\ExpireSidebarsObserver
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->historyHelperMock = $this->getMock(
            '\Magento\PersistentHistory\Helper\Data',
            ['isCompareProductsPersist', 'isComparedProductsPersist'],
            [],
            '',
            false
        );
        $this->compareItemMock = $this->getMock('\Magento\Catalog\Model\Product\Compare\Item', [], [], '', false);
        $this->comparedFactoryMock = $this->getMock(
            '\Magento\Reports\Model\Product\Index\ComparedFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->viewedFactoryMock = $this->getMock(
            '\Magento\Reports\Model\Product\Index\ViewedFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->subject = $objectManager->getObject(
            'Magento\PersistentHistory\Observer\ExpireSidebarsObserver',
            [
                'ePersistentData' => $this->historyHelperMock,
                'compareItem' => $this->compareItemMock,
                'comparedFactory' => $this->comparedFactoryMock,
                'viewedFactory' => $this->viewedFactoryMock
            ]
        );
    }

    public function testSidebarExpireDataIfCompareProductsNotPersistAndComparedProductsNotPersist()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->historyHelperMock->expects($this->once())
            ->method('isCompareProductsPersist')
            ->will($this->returnValue(false));
        $this->historyHelperMock->expects($this->exactly(2))
            ->method('isComparedProductsPersist')
            ->will($this->returnValue(false));
        $this->subject->execute($observerMock);
    }

    public function testSidebarExpireDataIfComparedProductsNotPersist()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->historyHelperMock->expects($this->once())
            ->method('isCompareProductsPersist')
            ->will($this->returnValue(true));

        $this->compareItemMock->expects($this->once())->method('bindCustomerLogout')->will($this->returnSelf());

        $this->historyHelperMock->expects($this->exactly(2))
            ->method('isComparedProductsPersist')
            ->will($this->returnValue(false));
        $this->subject->execute($observerMock);
    }

    public function testSidebarExpireDataIfCompareProductsNotPersist()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->historyHelperMock->expects($this->once())
            ->method('isCompareProductsPersist')
            ->will($this->returnValue(false));

        $this->historyHelperMock->expects($this->exactly(2))
            ->method('isComparedProductsPersist')
            ->will($this->returnValue(true));

        $comparedMock = $this->getMock('\Magento\Reports\Model\Product\Index\Compared', [], [], '', false);
        $comparedMock->expects($this->once())->method('purgeVisitorByCustomer')->will($this->returnSelf());
        $comparedMock->expects($this->once())->method('calculate')->will($this->returnSelf());
        $this->comparedFactoryMock->expects($this->once())->method('create')->will($this->returnValue($comparedMock));

        $viewedMock = $this->getMock('\Magento\Reports\Model\Product\Index\Viewed', [], [], '', false);
        $viewedMock->expects($this->once())->method('purgeVisitorByCustomer')->will($this->returnSelf());
        $viewedMock->expects($this->once())->method('calculate')->will($this->returnSelf());
        $this->viewedFactoryMock->expects($this->once())->method('create')->will($this->returnValue($viewedMock));

        $this->subject->execute($observerMock);
    }

    public function testSidebarExpireDataSuccess()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->historyHelperMock->expects($this->once())
            ->method('isCompareProductsPersist')
            ->will($this->returnValue(true));

        $this->compareItemMock->expects($this->once())->method('bindCustomerLogout')->will($this->returnSelf());

        $this->historyHelperMock->expects($this->exactly(2))
            ->method('isComparedProductsPersist')
            ->will($this->returnValue(true));

        $comparedMock = $this->getMock('\Magento\Reports\Model\Product\Index\Compared', [], [], '', false);
        $comparedMock->expects($this->once())->method('purgeVisitorByCustomer')->will($this->returnSelf());
        $comparedMock->expects($this->once())->method('calculate')->will($this->returnSelf());
        $this->comparedFactoryMock->expects($this->once())->method('create')->will($this->returnValue($comparedMock));

        $viewedMock = $this->getMock('\Magento\Reports\Model\Product\Index\Viewed', [], [], '', false);
        $viewedMock->expects($this->once())->method('purgeVisitorByCustomer')->will($this->returnSelf());
        $viewedMock->expects($this->once())->method('calculate')->will($this->returnSelf());
        $this->viewedFactoryMock->expects($this->once())->method('create')->will($this->returnValue($viewedMock));

        $this->subject->execute($observerMock);
    }
}
