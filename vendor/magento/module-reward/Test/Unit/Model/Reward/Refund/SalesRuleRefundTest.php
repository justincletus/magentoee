<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reward\Test\Unit\Model\Reward\Refund;

class SalesRuleRefundTest extends \PHPUnit_Framework_TestCase
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
    protected $rewardHelperMock;

    /**
     * @var \Magento\Reward\Model\Reward\Refund\SalesRuleRefund
     */
    protected $subject;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->rewardFactoryMock = $this->getMock(
            '\Magento\Reward\Model\RewardFactory',
            ['create', '__wakeup'],
            [],
            '',
            false
        );
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->rewardHelperMock = $this->getMock('\Magento\Reward\Helper\Data', [], [], '', false);
        $this->subject = $this->objectManager->getObject(
            'Magento\Reward\Model\Reward\Refund\SalesRuleRefund',
            [
                'rewardFactory' => $this->rewardFactoryMock,
                'storeManager' => $this->storeManagerMock,
                'rewardHelper' => $this->rewardHelperMock
            ]
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testRefundSuccess()
    {
        $websiteId = 2;
        $customerId = 10;
        $creditmemoTotalQty = 5;

        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            [
                'getRewardSalesrulePoints',
                '__wakeup',
                'getCreditmemosCollection',
                'getTotalQtyOrdered',
                'getStoreId',
                'getCustomerId'
            ],
            [],
            '',
            false
        );
        $creditmemoMock = $this->getMock(
            '\Magento\Sales\Model\Order\Creditmemo',
            [
                'getTotalQty',
                '__wakeup',
                'getOrder',
                'getAutomaticallyCreated',
                'setRewardPointsBalanceRefund',
                'getRewardPointsBalance'
            ],
            [],
            '',
            false
        );

        $creditmemoMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $creditmemoMock->expects($this->atLeastOnce())
            ->method('getTotalQty')
            ->will($this->returnValue($creditmemoTotalQty));

        $creditmemo = $this->getMock(
            '\Magento\Sales\Model\Order\Creditmemo',
            ['__wakeup', 'getData', 'getAllItems'],
            [],
            '',
            false
        );
        $creditmemoCollectionMock = $this->objectManager->getCollectionMock(
            'Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection',
            [$creditmemo]
        );
        $orderMock->expects($this->atLeastOnce())
            ->method('getCreditmemosCollection')
            ->will($this->returnValue($creditmemoCollectionMock));
        $itemMock = $this->getMock('\Magento\Sales\Model\Order\Creditmemo\Item', ['getQty', '__wakeup'], [], '', false);
        $creditmemo->expects($this->atLeastOnce())->method('getAllItems')->will($this->returnValue([$itemMock]));

        $itemMock->expects($this->atLeastOnce())->method('getQty')->will($this->returnValue(5));
        $creditmemoMock->expects($this->exactly(2))->method('getAutomaticallyCreated')->will($this->returnValue(true));
        $this->rewardHelperMock->expects($this->once())->method('isAutoRefundEnabled')->will($this->returnValue(true));

        $creditmemoMock->expects($this->once())->method('getRewardPointsBalance')->will($this->returnValue(100));
        $creditmemoMock->expects($this->once())
            ->method('setRewardPointsBalanceRefund')
            ->with(100)
            ->will($this->returnSelf());

        $orderMock->expects($this->exactly(3))->method('getRewardSalesrulePoints')->will($this->returnValue(200));
        $orderMock->expects($this->once())->method('getTotalQtyOrdered')->will($this->returnValue(10));
        $rewardMock = $this->getMock(
            '\Magento\Reward\Model\Reward',
            ['__wakeup', 'setActionEntity', 'loadByCustomer', 'getPointsBalance', 'save'],
            [],
            '',
            false
        );
        $this->rewardFactoryMock->expects($this->exactly(2))->method('create')->will($this->returnValue($rewardMock));
        $orderMock->expects($this->exactly(2))->method('getStoreId')->will($this->returnValue(1));

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->storeManagerMock->expects($this->exactly(2))->method('getStore')->will($this->returnValue($storeMock));
        $storeMock->expects($this->exactly(2))->method('getWebsiteId')->will($this->returnValue($websiteId));
        $orderMock->expects($this->exactly(2))->method('getCustomerId')->will($this->returnValue($customerId));

        $rewardMock->expects($this->once())->method('loadByCustomer')->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('getPointsBalance')->will($this->returnValue(500));
        $rewardMock->expects($this->once())->method('setActionEntity')->with($orderMock)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('save')->will($this->returnSelf());
        $this->subject->refund($creditmemoMock);
    }

    public function testRefundWhenAutoRefundDisabled()
    {
        $orderMock = $this->getMock('\Magento\Sales\Model\Order', [], [], '', false);
        $creditmemoMock = $this->getMock(
            '\Magento\Sales\Model\Order\Creditmemo',
            [
                '__wakeup',
                'getOrder',
                'getAutomaticallyCreated',
                'getRewardPointsBalance',
                'setRewardPointsBalanceRefund'
            ],
            [],
            '',
            false
        );

        $creditmemoMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $creditmemoMock->expects($this->exactly(2))->method('getAutomaticallyCreated')->will($this->returnValue(true));
        $creditmemoMock->expects($this->once())->method('getRewardPointsBalance')->will($this->returnValue(100));
        $creditmemoMock->expects($this->once())
            ->method('setRewardPointsBalanceRefund')
            ->with(100)
            ->will($this->returnSelf());

        $this->rewardHelperMock->expects($this->once())->method('isAutoRefundEnabled')->will($this->returnValue(false));
        $this->subject->refund($creditmemoMock);
    }

    public function testRefundWhenSalesRulePointsIsZero()
    {
        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['getRewardSalesrulePoints', '__wakeup'],
            [],
            '',
            false
        );
        $creditmemoMock = $this->getMock(
            '\Magento\Sales\Model\Order\Creditmemo',
            [
                '__wakeup',
                'getOrder',
                'getAutomaticallyCreated',
                'getRewardPointsBalance',
                'setRewardPointsBalanceRefund'
            ],
            [],
            '',
            false
        );

        $creditmemoMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $creditmemoMock->expects($this->exactly(2))->method('getAutomaticallyCreated')->will($this->returnValue(true));
        $creditmemoMock->expects($this->once())->method('getRewardPointsBalance')->will($this->returnValue(100));
        $creditmemoMock->expects($this->once())
            ->method('setRewardPointsBalanceRefund')
            ->with(100)
            ->will($this->returnSelf());

        $this->rewardHelperMock->expects($this->once())->method('isAutoRefundEnabled')->will($this->returnValue(true));
        $orderMock->expects($this->once())->method('getRewardSalesrulePoints')->will($this->returnValue(0));

        $this->subject->refund($creditmemoMock);
    }

    public function testPartialRefund()
    {
        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['getRewardSalesrulePoints', '__wakeup', 'getTotalQtyOrdered', 'getCreditmemosCollection'],
            [],
            '',
            false
        );
        $creditmemoMock = $this->getMock(
            '\Magento\Sales\Model\Order\Creditmemo',
            [
                '__wakeup',
                'getOrder',
                'getAutomaticallyCreated',
                'getRewardPointsBalance',
                'setRewardPointsBalanceRefund',
                'getTotalQty'
            ],
            [],
            '',
            false
        );

        $creditmemoMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $creditmemoMock->expects($this->exactly(2))->method('getAutomaticallyCreated')->will($this->returnValue(true));
        $creditmemoMock->expects($this->once())->method('getRewardPointsBalance')->will($this->returnValue(100));
        $creditmemoMock->expects($this->once())
            ->method('setRewardPointsBalanceRefund')
            ->with(100)
            ->will($this->returnSelf());

        $this->rewardHelperMock->expects($this->once())->method('isAutoRefundEnabled')->will($this->returnValue(true));

        $orderMock->expects($this->once())->method('getRewardSalesrulePoints')->will($this->returnValue(100));
        $orderMock->expects($this->once())->method('getTotalQtyOrdered')->will($this->returnValue(10));

        $creditmemoMock->expects($this->atLeastOnce())->method('getTotalQty')->will($this->returnValue(5));

        $creditmemo = $this->getMock(
            '\Magento\Sales\Model\Order\Creditmemo',
            ['__wakeup', 'getData', 'getAllItems'],
            [],
            '',
            false
        );
        $creditmemoCollectionMock = $this->objectManager->getCollectionMock(
            'Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection',
            [$creditmemo]
        );
        $orderMock->expects($this->atLeastOnce())
            ->method('getCreditmemosCollection')
            ->will($this->returnValue($creditmemoCollectionMock));

        $itemMock = $this->getMock('\Magento\Sales\Model\Order\Creditmemo\Item', ['getQty', '__wakeup'], [], '', false);
        $itemMock->expects($this->atLeastOnce())->method('getQty')->will($this->returnValue(3));
        $creditmemo->expects($this->atLeastOnce())->method('getAllItems')->will($this->returnValue([$itemMock]));

        $this->subject->refund($creditmemoMock);
    }
}
