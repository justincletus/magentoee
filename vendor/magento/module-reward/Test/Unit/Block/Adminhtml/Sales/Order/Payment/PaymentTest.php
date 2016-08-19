<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reward\Test\Unit\Block\Adminhtml\Sales\Order\Payment;

class PaymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Block\Adminhtml\Sales\Order\Create\Payment
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCreateMock;

    public function setUp()
    {
        $this->rewardFactoryMock = $this->getMockBuilder('\Magento\Reward\Model\RewardFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $contextMock = $this->getMock('\Magento\Backend\Block\Template\Context', [], [], '', false);
        $this->orderCreateMock = $this->getMock('\Magento\Sales\Model\AdminOrder\Create', [], [], '', false);
        $rewardDataMock = $this->getMock('\Magento\Reward\Helper\Data', [], [], '', false);
        $converterMock = $this->getMock('\Magento\Framework\Api\ExtensibleDataObjectConverter', [], [], '', false);

        $this->model = new \Magento\Reward\Block\Adminhtml\Sales\Order\Create\Payment(
            $contextMock,
            $rewardDataMock,
            $this->orderCreateMock,
            $this->rewardFactoryMock,
            $converterMock
        );
    }

    public function testGetReward()
    {
        $rewardMock = $this->getMock(
            '\Magento\Reward\Model\Reward',
            ['setStore', 'setCustomer', 'loadByCustomer'],
            [],
            '',
            false
        );
        $quoteMock = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false);
        $customerMock = $this->getMock('\Magento\Customer\Model\Data\Customer', [], [], '', false);
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->orderCreateMock->expects($this->any())->method('getQuote')->willReturn($quoteMock);

        $this->model->setData('reward', false);

        $quoteMock->expects($this->once())->method('getCustomer')->willReturn($customerMock);
        $this->rewardFactoryMock->expects($this->once())->method('create')->willReturn($rewardMock);
        $rewardMock->expects($this->once())->method('setCustomer')->with($customerMock)->willReturnSelf();
        $quoteMock->expects($this->once())->method('getStore')->willReturn($storeMock);
        $rewardMock->expects($this->once())->method('setStore')->with($storeMock);
        $rewardMock->expects($this->once())->method('loadByCustomer');

        $this->assertEquals($rewardMock, $this->model->getReward());
    }

    public function testGetRewardWithExistingReward()
    {
        $rewardMock = $this->getMock('\Magento\Reward\Model\Reward', [], [], '', false);
        $this->model->setData('reward', $rewardMock);
        $this->rewardFactoryMock->expects($this->never())->method('create');

        $this->assertEquals($rewardMock, $this->model->getReward());
    }
}
