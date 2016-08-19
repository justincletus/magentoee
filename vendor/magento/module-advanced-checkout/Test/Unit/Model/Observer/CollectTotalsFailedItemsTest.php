<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdvancedCheckout\Test\Unit\Model\Observer;

class CollectTotalsFailedItemsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CollectTotalsFailedItems
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cartMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemProcessorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    protected function setUp()
    {
        $this->cartMock = $this->getMock('Magento\AdvancedCheckout\Model\Cart', [], [], '', false);
        $this->itemProcessorMock =
            $this->getMock('Magento\AdvancedCheckout\Model\FailedItemProcessor', [], [], '', false);
        $this->observerMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);

        $this->model = new \Magento\AdvancedCheckout\Model\Observer\CollectTotalsFailedItems($this->cartMock, $this->itemProcessorMock);
    }

    public function testExecuteWithEmptyAffectedItems()
    {
        $this->cartMock->expects($this->once())->method('getFailedItems')->will($this->returnValue([]));
        $this->itemProcessorMock->expects($this->never())->method('process');

        $this->model->execute($this->observerMock);
    }

    public function testExecuteWithNonEmptyAffectedItems()
    {
        $this->cartMock->expects($this->once())->method('getFailedItems')->will($this->returnValue(['not empty']));
        $this->itemProcessorMock->expects($this->once())->method('process');

        $this->model->execute($this->observerMock);
    }
}
