<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Rma\Test\Unit\Model\Rma;

/**
 * Class CreateTest
 *
 * @package Magento\Rma\Model\Rma
 */
class CreateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerFactoryMock;

    /**
     * @var \Magento\Sales\Model\OrderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderFactoryMock;

    /**
     * @var \Magento\Customer\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMock;

    /**
     * @var \Magento\Rma\Model\Rma\Create
     */
    protected $rmaModel;

    public function setUp()
    {
        $this->customerFactoryMock = $this->getMock(
            'Magento\Customer\Model\CustomerFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->orderFactoryMock = $this->getMock('Magento\Sales\Model\OrderFactory', ['create'], [], '', false);
        $this->customerMock = $this->getMock('Magento\Customer\Model\Customer', ['__wakeup', 'load'], [], '', false);
        $data = ['order_id' => 1000000013, 'customer_id' => 2];

        $this->rmaModel = new \Magento\Rma\Model\Rma\Create($this->customerFactoryMock, $this->orderFactoryMock, $data);

        $this->customerMock->expects($this->once())
            ->method('load')
            ->with($this->rmaModel->getCustomerId())
            ->will($this->returnSelf());
        $this->customerFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->customerMock));
    }

    public function testGetCustomer()
    {
        $this->assertEquals($this->customerMock, $this->rmaModel->getCustomer());
    }

    public function testGetCustomerNoId()
    {
        $this->mockOrder($this->rmaModel->getCustomerId());
        $this->rmaModel->unsetData('customer_id');

        $this->assertEquals($this->customerMock, $this->rmaModel->getCustomer());
    }

    /**
     * Get Order Mock
     *
     * @param int $customerId
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function mockOrder($customerId)
    {
        $orderMock = $this->getMock('Magento\Sales\Model\Order', ['__wakeup', 'load', 'getCustomerId'], [], '', false);
        $this->orderFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($orderMock));
        $orderMock->expects($this->once())
            ->method('load')
            ->with($this->rmaModel->getOrderId())
            ->will($this->returnSelf());
        $orderMock->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue($customerId));
        return $orderMock;
    }
}
