<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\AdvancedCheckout\Test\Unit\Model\Observer;

class AddCartLinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AddCartLink
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cartMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventMock;

    protected function setUp()
    {
        $this->cartMock = $this->getMock('Magento\AdvancedCheckout\Model\Cart', [], [], '', false);
        $this->observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->eventMock = $this->getMock('\Magento\Framework\Event', [], [], '', false);

        $this->model = new \Magento\AdvancedCheckout\Model\Observer\AddCartLink($this->cartMock);
    }

    public function testExecuteWhenBlockIsNotSidebar()
    {
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $blockMock = $this->getMock('\Magento\Checkout\Block\Cart', [], [], '', false);
        $this->eventMock->expects($this->once())->method('getBlock')->will($this->returnValue($blockMock));
        $this->cartMock->expects($this->never())->method('getFailedItems');

        $this->model->execute($this->observerMock);
    }

    public function testExecuteWhenFailedItemsCountIsZero()
    {
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $blockMock = $this->getMock('\Magento\Checkout\Block\Cart\Sidebar', [], [], '', false);
        $this->eventMock->expects($this->once())->method('getBlock')->will($this->returnValue($blockMock));
        $this->cartMock->expects($this->once())->method('getFailedItems')->will($this->returnValue([]));
        $blockMock->expects($this->never())->method('setAllowCartLink');

        $this->model->execute($this->observerMock);
    }

    public function testExecute()
    {
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $blockMock = $this->getMock('\Magento\Checkout\Block\Cart\Sidebar',
            [
                'setAllowCartLink',
                'setCartEmptyMessage',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->eventMock->expects($this->once())->method('getBlock')->will($this->returnValue($blockMock));
        $this->cartMock->expects($this->once())->method('getFailedItems')->will($this->returnValue(['one', 'two']));
        $blockMock->expects($this->once())->method('setAllowCartLink')->with(true);
        $blockMock->expects($this->once())->method('setCartEmptyMessage')->with('2 item(s) need your attention.');

        $this->model->execute($this->observerMock);
    }
}
