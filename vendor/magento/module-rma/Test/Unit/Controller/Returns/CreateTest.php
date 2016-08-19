<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Test\Unit\Controller\Returns;

class CreateTest extends \Magento\Rma\Test\Unit\Controller\ReturnsTest
{
    /**
     * @var string
     */
    protected $name = 'Create';

    public function testCreateAction()
    {
        $orderId = 2;
        $customerId = 5;
        $post = ['customer_custom_email' => true, 'items' => ['1', '2'], 'rma_comment' => 'comment'];

        $this->request->expects($this->once())
            ->method('getParam')
            ->with('order_id')
            ->will($this->returnValue($orderId));

        $order = $this->getMock(
            'Magento\Sales\Model\Order',
            ['__wakeup', 'getCustomerId', 'load', 'getId'],
            [],
            '',
            false
        );
        $order->expects($this->once())
            ->method('load')
            ->with($orderId)
            ->will($this->returnSelf());
        $order->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($orderId));
        $order->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue($customerId));

        $dateTime = $this->getMock('Magento\Framework\Stdlib\DateTime\DateTime', [], [], '', false);
        $rma = $this->getMock('Magento\Rma\Model\Rma', [], [], '', false);
        $rma->expects($this->once())
            ->method('setData')
            ->will($this->returnSelf());
        $rma->expects($this->once())
            ->method('saveRma')
            ->will($this->returnSelf());
        $history1 = $this->getMock('Magento\Rma\Model\Rma\Status\History', [], [], '', false);
        $history2 = $this->getMock('Magento\Rma\Model\Rma\Status\History', [], [], '', false);
        $rmaHelper = $this->getMock('Magento\Rma\Helper\Data', [], [], '', false);
        $rmaHelper->expects($this->once())
            ->method('canCreateRma')
            ->with($orderId)
            ->will($this->returnValue(true));
        $session = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);
        $session->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue($customerId));

        $this->objectManager->expects($this->at(0))
            ->method('create')
            ->with('Magento\Sales\Model\Order')
            ->will($this->returnValue($order));
        $this->objectManager->expects($this->at(1))
            ->method('get')
            ->with('Magento\Rma\Helper\Data')
            ->will($this->returnValue($rmaHelper));
        $this->objectManager->expects($this->at(2))
            ->method('get')
            ->with('Magento\Framework\Stdlib\DateTime\DateTime')
            ->will($this->returnValue($dateTime));
        $this->objectManager->expects($this->at(3))
            ->method('get')
            ->with('Magento\Customer\Model\Session')
            ->will($this->returnValue($session));
        $this->objectManager->expects($this->at(4))
            ->method('create')
            ->with('Magento\Rma\Model\Rma')
            ->will($this->returnValue($rma));
        $this->objectManager->expects($this->at(5))
            ->method('create')
            ->with('Magento\Rma\Model\Rma\Status\History')
            ->will($this->returnValue($history1));
        $this->objectManager->expects($this->at(6))
            ->method('create')
            ->with('Magento\Rma\Model\Rma\Status\History')
            ->will($this->returnValue($history2));

        $this->request->expects($this->once())
            ->method('getPostValue')
            ->will($this->returnValue($post));

        $this->controller->execute();
    }
}
