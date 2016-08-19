<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Observer;

use Magento\Reward\Model\Reward;

class RedeemForOrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Observer\RedeemForOrder
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
    protected $_validatorMock;

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
        $this->_validatorMock = $this->getMock(
            'Magento\Reward\Model\Reward\Balance\Validator',
            [],
            [],
            '',
            false
        );

        $this->_observerMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);

        $this->_model = new \Magento\Reward\Observer\RedeemForOrder(
            $this->_restrictionMock,
            $this->_storeManagerMock,
            $this->_modelFactoryMock,
            $this->_validatorMock
        );
    }

    public function testRedeemForOrderIfRestrictionNotAllowed()
    {
        $this->_restrictionMock->expects($this->once())->method('isAllowed')->will($this->returnValue(false));
        $this->_observerMock->expects($this->never())->method('getEvent');
        $this->_model->execute($this->_observerMock);
    }

    public function testRedeemForOrderIfRewardCurrencyAmountAboveNull()
    {
        $baseRewardCurrencyAmount = 1;
        $rewardPointsBalance = 100;
        $this->_restrictionMock->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $order = $this->getMock(
            'Magento\Sales\Model\Order',
            ['__wakeup', 'setBaseRewardCurrencyAmount', 'setRewardPointsBalance'],
            [],
            '',
            false
        );
        $quote = $this->getMock(
            '\Magento\Quote\Model\Quote',
            ['getBaseRewardCurrencyAmount', 'getRewardPointsBalance'],
            [],
            '',
            false
        );
        $event = $this->getMock('Magento\Framework\Event', ['getOrder', 'getQuote'], [], '', false);
        $this->_observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getOrder')->will($this->returnValue($order));
        $event->expects($this->once())->method('getQuote')->will($this->returnValue($quote));
        $quote->expects($this->atLeastOnce())->method('getBaseRewardCurrencyAmount')
            ->willReturn($baseRewardCurrencyAmount);
        $model = $this->getMock('Magento\Reward\Model\Reward', [], [], '', false);
        $this->_modelFactoryMock->expects($this->once())->method('create')->will($this->returnValue($model));
        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $this->_storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($store));
        $store->expects($this->once())->method('getWebsiteId');
        $quote->expects($this->atLeastOnce())->method('getRewardPointsBalance')->willReturn($rewardPointsBalance);
        $order->expects($this->once())->method('setBaseRewardCurrencyAmount')->with($baseRewardCurrencyAmount);
        $order->expects($this->once())->method('setRewardPointsBalance')->with($rewardPointsBalance);
        $this->_model->execute($this->_observerMock);
    }

    public function testRedeemForOrderIfRewardCurrencyAmountBelowNull()
    {
        $baseRewardCurrencyAmount = -1;
        $this->_restrictionMock->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $order = $this->getMock('Magento\Sales\Model\Order', ['__wakeup'], [], '', false);
        $quote = $this->getMock('\Magento\Quote\Model\Quote', ['getBaseRewardCurrencyAmount'], [], '', false);
        $event = $this->getMock('Magento\Framework\Event', ['getOrder', 'getQuote'], [], '', false);
        $this->_observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getOrder')->will($this->returnValue($order));
        $event->expects($this->once())->method('getQuote')->will($this->returnValue($quote));
        $quote->expects($this->once())->method('getBaseRewardCurrencyAmount')->willReturn($baseRewardCurrencyAmount);
        $this->_model->execute($this->_observerMock);
    }
}
