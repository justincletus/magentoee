<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerBalance\Test\Unit\Observer;

use Magento\CustomerBalance\Observer\CustomerSaveAfterObserver;

class CustomerSaveAfterObserverTest extends \PHPUnit_Framework_TestCase
{
    /** @var CustomerSaveAfterObserver */
    protected $observer;

    /** @var \Magento\CustomerBalance\Model\BalanceFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $balanceFactory;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    /** @var \Magento\CustomerBalance\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $customerBalanceData;

    /** @var \Magento\Framework\Event\Observer|\PHPUnit_Framework_MockObject_MockObject */
    protected $eventObserver;

    /** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    /** @var \Magento\Customer\Api\Data\CustomerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $customer;

    /** @var \Magento\CustomerBalance\Model\Balance|\PHPUnit_Framework_MockObject_MockObject */
    protected $balance;

    /** @var \Magento\Store\Api\Data\StoreInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $store;

    public function setUp()
    {
        $this->balanceFactory = $this->getMock(
            'Magento\CustomerBalance\Model\BalanceFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->storeManager = $this->getMockForAbstractClass(
            'Magento\Store\Model\StoreManagerInterface',
            [],
            '',
            false
        );
        $this->customerBalanceData = $this->getMock('Magento\CustomerBalance\Helper\Data', [], [], '', false);

        $this->observer = new CustomerSaveAfterObserver(
            $this->balanceFactory,
            $this->storeManager,
            $this->customerBalanceData
        );

        $this->eventObserver = $this->getMock(
            'Magento\Framework\Event\Observer',
            ['getRequest', 'getCustomer'],
            [],
            '',
            false
        );
        $this->request = $this->getMockForAbstractClass(
            'Magento\Framework\App\RequestInterface',
            [],
            '',
            false,
            false,
            true,
            ['getPost']
        );
        $this->customer = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\CustomerInterface',
            [],
            '',
            false
        );
        $this->balance = $this->getMock(
            'Magento\CustomerBalance\Model\Balance',
            ['setCustomer', 'setWebsiteId', 'setAmountDelta', 'setComment', 'setNotifyByEmail', 'save'],
            [],
            '',
            false
        );
        $this->store = $this->getMockForAbstractClass('Magento\Store\Api\Data\StoreInterface', [], '', false);
    }

    public function testExecuteWithDisabledCustomerBalance()
    {
        $this->customerBalanceData->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);
        $this->assertNull($this->observer->execute($this->eventObserver));
    }

    public function testExecuteWithEmailNotification()
    {
        $post = [
            'amount_delta' => 1000,
            'website_id' => 1,
            'store_id' => 1,
            'comment' => 'comment',
            'notify_by_email' => 1,
        ];
        $this->customerBalanceData->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);
        $this->eventObserver->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->request);
        $this->request->expects($this->once())
            ->method('getPost')
            ->with('customerbalance')
            ->willReturn($post);
        $this->eventObserver->expects($this->once())
            ->method('getCustomer')
            ->willReturn($this->customer);
        $this->balanceFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->balance);
        $this->balance->expects($this->once())
            ->method('setCustomer')
            ->with($this->customer)
            ->willReturnSelf();
        $this->balance->expects($this->once())
            ->method('setWebsiteId')
            ->with(1)
            ->willReturnSelf();
        $this->balance->expects($this->once())
            ->method('setAmountDelta')
            ->with(1000)
            ->willReturnSelf();
        $this->balance->expects($this->once())
            ->method('setComment')
            ->with('comment')
            ->willReturnSelf();
        $this->balance->expects($this->once())
            ->method('setNotifyByEmail')
            ->with(true, 1);
        $this->balance->expects($this->once())
            ->method('save');

        $this->observer->execute($this->eventObserver);
    }

    public function testExecuteWithEmailNotificationAndSingleStoreMode()
    {
        $storeId = 1;
        $post = [
            'amount_delta' => 1000,
            'website_id' => 1,
            'comment' => 'comment',
            'notify_by_email' => 1,
        ];
        $this->customerBalanceData->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->eventObserver->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->request);
        $this->request->expects($this->once())
            ->method('getPost')
            ->with('customerbalance')
            ->willReturn($post);
        $this->eventObserver->expects($this->once())
            ->method('getCustomer')
            ->willReturn($this->customer);
        $this->balanceFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->balance);
        $this->balance->expects($this->once())
            ->method('setCustomer')
            ->with($this->customer)
            ->willReturnSelf();
        $this->balance->expects($this->once())
            ->method('setWebsiteId')
            ->with(1)
            ->willReturnSelf();
        $this->balance->expects($this->once())
            ->method('setAmountDelta')
            ->with(1000)
            ->willReturnSelf();
        $this->balance->expects($this->once())
            ->method('setComment')
            ->with('comment')
            ->willReturnSelf();
        $this->storeManager->expects($this->once())
            ->method('isSingleStoreMode')
            ->willReturn(true);
        $this->storeManager->expects($this->once())
            ->method('getStores')
            ->willReturn([$this->store]);
        $this->store->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->balance->expects($this->once())
            ->method('setNotifyByEmail')
            ->with(true, $storeId);
        $this->balance->expects($this->once())
            ->method('save');

        $this->observer->execute($this->eventObserver);
    }
}
