<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reward\Test\Unit\Model\Reward;

use Magento\Reward\Model\Reward;

class ReverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardResourceFactoryMock;

    /**
     * @var \Magento\Reward\Model\Reward\Reverter
     */
    protected $model;

    protected function setUp()
    {
        $this->rewardFactoryMock = $this->getMock('\Magento\Reward\Model\RewardFactory', ['create'], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->rewardResourceFactoryMock = $this->getMock(
            '\Magento\Reward\Model\ResourceModel\RewardFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->model = new \Magento\Reward\Model\Reward\Reverter(
            $this->storeManagerMock,
            $this->rewardFactoryMock,
            $this->rewardResourceFactoryMock
        );
    }

    public function testRevertRewardPointsForOrderPositive()
    {
        $customerId = 1;
        $storeId = 2;
        $websiteId = 100;

        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['__wakeup', 'getCustomerId', 'getStoreId', 'getRewardPointsBalance'],
            [],
            '',
            false
        );

        $storeMock = $this->getMock('\Magento\Store\Model\Store', ['getWebsiteId', '__wakeup'], [], '', false);
        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue($websiteId));
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->will($this->returnValue($storeMock));

        $rewardMock = $this->getMock(
            '\Magento\Reward\Model\Reward',
            [
                '__wakeup',
                'setCustomerId',
                'setWebsiteId',
                'setPointsDelta',
                'setAction',
                'setActionEntity',
                'updateRewardPoints'
            ],
            [],
            '',
            false
        );
        $this->rewardFactoryMock->expects($this->once())->method('create')->will($this->returnValue($rewardMock));

        $rewardMock->expects($this->once())->method('setCustomerId')->with($customerId)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setWebsiteId')->with($websiteId)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setPointsDelta')->with(500)->will($this->returnSelf());
        $rewardMock->expects($this->once())
            ->method('setAction')
            ->with(\Magento\Reward\Model\Reward::REWARD_ACTION_REVERT)
            ->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setActionEntity')->with($orderMock)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('updateRewardPoints')->will($this->returnSelf());

        $orderMock->expects($this->exactly(2))->method('getCustomerId')->will($this->returnValue($customerId));
        $orderMock->expects($this->once())->method('getStoreId')->will($this->returnValue($storeId));
        $orderMock->expects($this->once())->method('getRewardPointsBalance')->will($this->returnValue(500));

        $this->assertEquals($this->model, $this->model->revertRewardPointsForOrder($orderMock));
    }

    public function testRevertRewardPointsIfNoCustomerId()
    {
        $orderMock = $this->getMock('\Magento\Sales\Model\Order', ['__wakeup', 'getCustomerId'], [], '', false);
        $orderMock->expects($this->once())->method('getCustomerId')->will($this->returnValue(null));
        $this->assertEquals($this->model, $this->model->revertRewardPointsForOrder($orderMock));
    }

    public function testRevertEarnedPointsForOrder()
    {
        $appliedRuleIds = '1,1,2';
        $ruleIds = [0 => 1, 2 => 2];
        $rewardRules = [['points_delta' => 10], ['points_delta' => 20]];
        $pointsDelta = -30;
        $customerId = 42;
        $storeId = 1;
        $websiteId = 1;

        $orderMock = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false);
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $rewardMock = $this->getMock(
            '\Magento\Reward\Model\Reward',
            ['setCustomerId', 'setWebsiteId', 'setPointsDelta', 'setAction', 'setActionEntity', 'updateRewardPoints'],
            [],
            '',
            false
        );

        $orderMock->expects($this->once())->method('getAppliedRuleIds')->willReturn($appliedRuleIds);
        $rewardResourceMock = $this->getMock('\Magento\Reward\Model\ResourceModel\Reward', [], [], '', false);
        $this->rewardResourceFactoryMock->expects($this->once())->method('create')->willReturn($rewardResourceMock);
        $rewardResourceMock->expects($this->once())->method('getRewardSalesRule')->with($ruleIds)
            ->willReturn($rewardRules);
        $orderMock->expects($this->once())->method('getCustomerIsGuest')->willReturn(false);

        $this->rewardFactoryMock->expects($this->once())->method('create')->willReturn($rewardMock);
        $orderMock->expects($this->once())->method('getCustomerId')->willReturn($customerId);
        $rewardMock->expects($this->once())->method('setCustomerId')->with($customerId)->willReturnSelf();
        $orderMock->expects($this->once())->method('getStoreId')->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())->method('getStore')->with($storeId)->willReturn($storeMock);
        $storeMock->expects($this->once())->method('getWebsiteId')->willReturn($websiteId);
        $rewardMock->expects($this->once())->method('setWebsiteId')->with($websiteId)->willReturnSelf();
        $rewardMock->expects($this->once())->method('setPointsDelta')->with($pointsDelta)->willReturnSelf();
        $rewardMock->expects($this->once())->method('setAction')->with(Reward::REWARD_ACTION_REVERT)->willReturnSelf();
        $rewardMock->expects($this->once())->method('setActionEntity')->with($orderMock)->willReturnSelf();
        $rewardMock->expects($this->once())->method('updateRewardPoints');

        $this->assertEquals($this->model, $this->model->revertEarnedRewardPointsForOrder($orderMock));
    }
}
