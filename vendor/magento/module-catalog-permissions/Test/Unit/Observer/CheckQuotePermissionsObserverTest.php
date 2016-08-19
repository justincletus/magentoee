<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Test\Unit\Observer;

use Magento\CatalogPermissions\Observer\CheckQuotePermissionsObserver;
use Magento\Framework\DataObject;

/**
 * Test for \Magento\CatalogPermissions\Observer\CheckQuotePermissionsObserver
 */
class CheckQuotePermissionsObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogPermissions\Observer\CheckQuotePermissionsObserver
     */
    protected $observer;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $permissionsConfig;

    /**
     * @var \Magento\CatalogPermissions\Model\Permission\Index|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $permissionIndex;

    /**
     * @var \Magento\Framework\Event\Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventObserverMock;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->permissionsConfig = $this->getMock('Magento\CatalogPermissions\App\ConfigInterface');
        $this->permissionIndex = $this->getMock('Magento\CatalogPermissions\Model\Permission\Index', [], [], '', false);

        $this->eventObserverMock = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $this->observer = new CheckQuotePermissionsObserver(
            $this->permissionsConfig,
            $this->getMock('Magento\Customer\Model\Session', [], [], '', false),
            $this->permissionIndex,
            $this->getMock('Magento\CatalogPermissions\Helper\Data', [], [], '', false)
        );
    }

    /**
     * @param int $step
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function preparationData($step = 0)
    {
        $quoteMock = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false);

        if ($step == 0) {
            $quoteMock->expects($this->exactly(3))
                ->method('getAllItems')
                ->will($this->returnValue([]));
        } else {
            $quoteItems = $this->getMock(
                'Magento\Eav\Model\Entity\Collection\AbstractCollection',
                ['getProductId', 'setDisableAddToCart', 'getParentItem', 'getDisableAddToCart'],
                [],
                '',
                false
            );

            $quoteItems->expects($this->exactly(5))
                ->method('getProductId')
                ->will($this->returnValue(1));

            $quoteItems->expects($this->once())
                ->method('getParentItem')
                ->will($this->returnValue(0));

            $quoteItems->expects($this->once())
                ->method('getDisableAddToCart')
                ->will($this->returnValue(0));

            $quoteMock->expects($this->exactly(3))
                ->method('getAllItems')
                ->will($this->returnValue([$quoteItems]));
        }

        if ($step == 1) {
            $this->permissionIndex->expects($this->exactly(1))
                ->method('getIndexForProduct')
                ->will($this->returnValue([]));
        } elseif ($step == 2) {
            $this->permissionIndex->expects($this->exactly(1))
                ->method('getIndexForProduct')
                ->will($this->returnValue([1 => true]));
        }

        $cartMock = $this->getMock('Magento\AdvancedCheckout\Model\Cart', [], [], '', false);
        $cartMock->expects($this->once())
            ->method('getQuote')
            ->will($this->returnValue($quoteMock));

        $eventMock = $this->getMock('Magento\Framework\Event', ['getCart'], [], '', false);
        $eventMock->expects($this->once())
            ->method('getCart')
            ->will($this->returnValue($cartMock));

        $observerMock = $this->getMock('Magento\Framework\Event\Observer', ['getEvent'], [], '', false);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($eventMock));

        return $observerMock;
    }

    /**
     * @return void
     */
    public function testCheckQuotePermissionsPermissionsConfigDisabled()
    {
        $this->permissionsConfig->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(false));

        $observerMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $this->assertEquals($this->observer, $this->observer->execute($observerMock));
    }

    /**
     * @param int $step
     * @dataProvider dataSteps
     * @return void
     */
    public function testCheckQuotePermissionsPermissionsConfigEnabled($step)
    {
        $this->permissionsConfig->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $observer = $this->preparationData($step);
        $this->assertEquals($this->observer, $this->observer->execute($observer));
    }

    /**
     * @return array
     */
    public function dataSteps()
    {
        return [[0], [1], [2]];
    }
}
