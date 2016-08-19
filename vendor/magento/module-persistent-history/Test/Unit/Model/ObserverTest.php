<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PersistentHistory\Test\Unit\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $persistentHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionHelperMock;

    /**
     * @var \Magento\PersistentHistory\Model\Observer
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->persistentHelperMock = $this->getMock(
            '\Magento\PersistentHistory\Helper\Data',
            [
                'isOrderedItemsPersist',
                'isViewedProductsPersist',
                'isComparedProductsPersist',
                'isCompareProductsPersist',
            ],
            [],
            '',
            false
        );
        $this->sessionHelperMock = $this->getMock(
            '\Magento\Persistent\Helper\Session',
            ['getSession'],
            [],
            '',
            false
        );
        $this->subject = $objectManager->getObject(
            'Magento\PersistentHistory\Model\Observer',
            ['ePersistentData' => $this->persistentHelperMock, 'persistentSession' => $this->sessionHelperMock]
        );
    }

    public function testInitReorderSidebarIfOrderItemsNotPersist()
    {
        $blockMock = $this->getMock('\Magento\Framework\View\Element\AbstractBlock', [], [], '', false);
        $this->persistentHelperMock->expects($this->once())
            ->method('isOrderedItemsPersist')
            ->will($this->returnValue(false));
        $this->subject->initReorderSidebar($blockMock);
    }

    public function testInitReorderSidebarSuccess()
    {
        $customerId = 100;
        $this->sessionHelperMock->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($this->getSessionMock()));

        $blockMock = $this->getMock(
            '\Magento\Framework\View\Element\AbstractBlock',
            ['setCustomerId', '__wakeup', 'initOrders'],
            [],
            '',
            false
        );
        $this->persistentHelperMock->expects($this->once())
            ->method('isOrderedItemsPersist')
            ->will($this->returnValue(true));

        $blockMock->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->will($this->returnSelf());
        $blockMock->expects($this->once())->method('initOrders')->will($this->returnSelf());
        $this->subject->initReorderSidebar($blockMock);
    }

    public function testEmulateViewedProductsIfProductsNotPersist()
    {
        $blockMock = $this->getMock('\Magento\Reports\Block\Product\Viewed', [], [], '', false);
        $this->persistentHelperMock->expects($this->once())
            ->method('isViewedProductsPersist')
            ->will($this->returnValue(false));
        $this->subject->emulateViewedProductsBlock($blockMock);
    }

    public function testEmulateViewedProductsSuccess()
    {
        $customerId = 100;
        $this->sessionHelperMock->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($this->getSessionMock()));

        $blockMock = $this->getMock(
            '\Magento\Reports\Block\Product\Viewed',
            ['getModel', 'setCustomerId', '__wakeup'],
            [],
            '',
            false
        );
        $this->persistentHelperMock->expects($this->once())
            ->method('isViewedProductsPersist')
            ->will($this->returnValue(true));

        $modelMock = $this->getMock(
            '\Magento\Reports\Model\Product\Index\AbstractIndex',
            ['setCustomerId', 'calculate', '__wakeup'],
            [],
            '',
            false
        );
        $modelMock->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->will($this->returnSelf());
        $modelMock->expects($this->once())->method('calculate')->will($this->returnSelf());

        $blockMock->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($modelMock));
        $blockMock->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->will($this->returnSelf());

        $this->subject->emulateViewedProductsBlock($blockMock);
    }

    public function testEmulateComparedProductsIfProductsNotPersist()
    {
        $blockMock = $this->getMock('\Magento\Reports\Block\Product\Compared', [], [], '', false);
        $this->persistentHelperMock->expects($this->once())
            ->method('isComparedProductsPersist')
            ->will($this->returnValue(false));
        $this->subject->emulateComparedProductsBlock($blockMock);
    }

    public function testEmulateComparedProductsSuccess()
    {
        $customerId = 100;
        $this->sessionHelperMock->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($this->getSessionMock()));

        $blockMock = $this->getMock(
            '\Magento\Reports\Block\Product\Compared',
            ['getModel', 'setCustomerId', '__wakeup'],
            [],
            '',
            false
        );
        $this->persistentHelperMock->expects($this->once())
            ->method('isComparedProductsPersist')
            ->will($this->returnValue(true));

        $modelMock = $this->getMock(
            '\Magento\Reports\Model\Product\Index\AbstractIndex',
            ['setCustomerId', 'calculate', '__wakeup'],
            [],
            '',
            false
        );
        $modelMock->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->will($this->returnSelf());
        $modelMock->expects($this->once())->method('calculate')->will($this->returnSelf());

        $blockMock->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($modelMock));
        $blockMock->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->will($this->returnSelf());

        $this->subject->emulateComparedProductsBlock($blockMock);
    }

    public function testEmulateCompareProductListIfProductsNotPersistent()
    {
        $blockMock = $this->getMock('\Magento\Catalog\Block\Product\Compare\ListCompare', [], [], '', false);
        $this->persistentHelperMock->expects($this->once())
            ->method('isCompareProductsPersist')
            ->will($this->returnValue(false));
        $this->subject->emulateCompareProductsListBlock($blockMock);
    }

    public function testEmulateCompareProductListSuccess()
    {
        $customerId = 100;
        $this->sessionHelperMock->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($this->getSessionMock()));

        $blockMock = $this->getMock(
            '\Magento\Catalog\Block\Product\Compare\ListCompare',
            ['setCustomerId'],
            [],
            '',
            false
        );
        $this->persistentHelperMock->expects($this->once())
            ->method('isCompareProductsPersist')
            ->will($this->returnValue(true));
        $blockMock->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->will($this->returnSelf());
        $this->subject->emulateCompareProductsListBlock($blockMock);
    }

    protected function getSessionMock()
    {
        $customerId = 100;
        $sessionMock = $this->getMock(
            '\Magento\Persistent\Model\Session',
            ['getCustomerId', '__wakeup'],
            [],
            '',
            false
        );
        $sessionMock->expects($this->once())->method('getCustomerId')->will($this->returnValue($customerId));
        return $sessionMock;
    }
}
