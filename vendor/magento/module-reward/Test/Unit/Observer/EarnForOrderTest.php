<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Observer;

use Magento\Reward\Model\Reward;

class EarnForOrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Observer\EarnForOrder
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_restrictionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modelFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardHelperMock;

    protected function setUp()
    {
        $this->_restrictionMock = $this->getMock('Magento\Reward\Observer\PlaceOrder\RestrictionInterface');
        $this->_storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', [], [], '', false);
        $this->rewardHelperMock = $this->getMock('\Magento\Reward\Helper\Data', [], [], '', false);
        $this->_modelFactoryMock = $this->getMock(
            'Magento\Reward\Model\RewardFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->_resourceFactoryMock = $this->getMock(
            'Magento\Reward\Model\ResourceModel\RewardFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->_observerMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);

        $this->_model = new \Magento\Reward\Observer\EarnForOrder(
            $this->_restrictionMock,
            $this->_storeManagerMock,
            $this->_modelFactoryMock,
            $this->_resourceFactoryMock,
            $this->rewardHelperMock
        );
    }

    public function testEarnForOrderRestricted()
    {
        $this->_restrictionMock->expects($this->once())->method('isAllowed')->willReturn(false);
        $this->_observerMock->expects($this->never())->method('getEvent');

        $this->_model->execute($this->_observerMock);
    }

    public function testEarnForOrder()
    {
        $apliedRuleIds = '1,1,2';
        $applicableRuleIds = [0 => 1, 2 => 2];
        $rules = [['points_delta' => 10],['points_delta' => 20]];
        $pointsDelta = array_sum(array_column($rules, 'points_delta'));
        $customerId = 42;
        $websiteId = 1;
        $historyEntry = __('Customer earned promotion extra %1.', $pointsDelta);
        $eventMock = $this->getMock('Magento\Framework\Event', ['getOrder'], [], '', false);
        $orderMock = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false);
        $rewardResourceMock = $this->getMock('\Magento\Reward\Model\ResourceModel\Reward', [], [], '', false);
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $historyMock = $this->getMock('\Magento\Sales\Model\Order\Status\History', ['save'], [], '', false);
        $rewardModelMock = $this->getMock(
            '\Magento\Reward\Model\Reward',
            ['setCustomerId', 'setWebsiteId', 'setPointsDelta', 'setAction', 'setActionEntity', 'updateRewardPoints'],
            [],
            '',
            false
        );

        $this->_observerMock->expects($this->once())->method('getEvent')->willReturn($eventMock);
        $eventMock->expects($this->once())->method('getOrder')->willReturn($orderMock);
        $orderMock->expects($this->once())->method('getAppliedRuleIds')->willReturn($apliedRuleIds);
        $this->_resourceFactoryMock->expects($this->once())->method('create')->willReturn($rewardResourceMock);
        $rewardResourceMock->expects($this->once())->method('getRewardSalesrule')->with($applicableRuleIds)
            ->willReturn($rules);
        $this->_modelFactoryMock->expects($this->once())->method('create')->willReturn($rewardModelMock);
        $orderMock->expects($this->once())->method('getCustomerId')->willReturn($customerId);
        $rewardModelMock->expects($this->once())->method('setCustomerId')->with($customerId)->willReturnSelf();
        $this->_storeManagerMock->expects($this->once())->method('getStore')->willReturn($storeMock);
        $storeMock->expects($this->once())->method('getWebsiteId')->willReturn($websiteId);
        $rewardModelMock->expects($this->once())->method('setWebsiteId')->with($websiteId)->willReturnSelf();
        $rewardModelMock->expects($this->once())->method('setPointsDelta')->with($pointsDelta)->willReturnSelf();
        $rewardModelMock->expects($this->once())->method('setAction')->with(Reward::REWARD_ACTION_SALESRULE)
            ->willReturnSelf();
        $rewardModelMock->expects($this->once())->method('setActionEntity')->with($orderMock)->willReturnSelf();
        $rewardModelMock->expects($this->once())->method('updateRewardPoints');

        $this->rewardHelperMock->expects($this->once())->method('formatReward')->with($pointsDelta)
            ->willReturn($pointsDelta);
        $orderMock->expects($this->once())->method('addStatusHistoryComment')->with($historyEntry)
            ->willReturn($historyMock);

        $this->_model->execute($this->_observerMock);
    }

    public function testEarnForOrderWithNoSalesRule()
    {
        $apliedRuleIds = '';
        $applicableRuleIds = [0 => null];
        $rules = [];
        $eventMock = $this->getMock('Magento\Framework\Event', ['getOrder'], [], '', false);
        $orderMock = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false);
        $rewardResourceMock = $this->getMock('\Magento\Reward\Model\ResourceModel\Reward', [], [], '', false);

        $this->_observerMock->expects($this->once())->method('getEvent')->willReturn($eventMock);
        $eventMock->expects($this->once())->method('getOrder')->willReturn($orderMock);
        $orderMock->expects($this->once())->method('getAppliedRuleIds')->willReturn($apliedRuleIds);
        $this->_resourceFactoryMock->expects($this->once())->method('create')->willReturn($rewardResourceMock);
        $rewardResourceMock->expects($this->once())->method('getRewardSalesrule')->with($applicableRuleIds)
            ->willReturn($rules);
        $this->_modelFactoryMock->expects($this->never())->method('create');

        $this->_model->execute($this->_observerMock);
    }
}
