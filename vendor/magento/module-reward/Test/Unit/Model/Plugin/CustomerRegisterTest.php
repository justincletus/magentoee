<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reward\Test\Unit\Model\Plugin;

use Magento\Reward\Model\Reward;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class CustomerRegisterTest
 */
class CustomerRegisterTest extends \PHPUnit_Framework_TestCase
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
    protected $rewardDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var \Magento\Reward\Model\Plugin\CustomerRegister
     */
    protected $subject;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $accountManagementMock;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new ObjectManager($this);

        $this->rewardDataMock = $this->getMock('\Magento\Reward\Helper\Data', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->rewardFactoryMock = $this->getMock('\Magento\Reward\Model\RewardFactory', ['create'], [], '', false);
        $this->loggerMock = $this->getMock('Psr\Log\LoggerInterface');
        $this->accountManagementMock = $this->getMock('\Magento\Customer\Model\AccountManagement', [], [], '', false);

        $this->subject = $objectManager->getObject(
            'Magento\Reward\Model\Plugin\CustomerRegister',
            [
                'rewardData' => $this->rewardDataMock,
                'storeManager' => $this->storeManagerMock,
                'rewardFactory' => $this->rewardFactoryMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    public function testUpdateRewardPointsWhenRewardDisabledInFront()
    {
        $customerMock = $this->getMock('\Magento\Customer\Model\Data\Customer', [], [], '', false);
        $proceed = function () use ($customerMock) {
            return $customerMock;
        };

        $this->rewardDataMock->expects($this->once())->method('isEnabledOnFront')->will($this->returnValue(false));

        $this->assertEquals(
            $customerMock,
            $this->subject->aroundCreateAccountWithPasswordHash(
                $this->accountManagementMock,
                $proceed,
                $customerMock,
                'hash'
            )
        );
    }

    public function testUpdateRewardPointsSuccess()
    {
        $notificationConfig = 1;
        $websiteId = 74;
        $storeId = 42;
        $customerMock = $this->getMock('\Magento\Customer\Model\Data\Customer', [], [], '', false);
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $rewardMock = $this->getMock(
            '\Magento\Reward\Model\Reward',
            ['setCustomer', 'setActionEntity', 'setStore', 'setAction', 'updateRewardPoints', '__wakeup'],
            [],
            '',
            false
        );
        $proceed = function () use ($customerMock) {
            return $customerMock;
        };

        $this->rewardDataMock->expects($this->once())->method('isEnabledOnFront')->willReturn(true);
        $this->storeManagerMock->expects($this->atLeastOnce())->method('getStore')->willReturn($storeMock);
        $storeMock->expects($this->once())->method('getWebsiteId')->willReturn($websiteId);
        $this->rewardDataMock->expects($this->once())->method('getNotificationConfig')
            ->with('subscribe_by_default', $websiteId)
            ->willReturn($notificationConfig);
        $customerMock->expects($this->exactly(2))->method('setCustomAttribute')->withConsecutive(
            ['reward_update_notification', $notificationConfig],
            ['reward_warning_notification', $notificationConfig]
        );

        $this->rewardFactoryMock->expects($this->once())->method('create')->willReturn($rewardMock);
        $rewardMock->expects($this->once())->method('setCustomer')->with($customerMock)->willReturnSelf();
        $rewardMock->expects($this->once())->method('setActionEntity')->with($customerMock)->willReturnSelf();
        $storeMock->expects($this->once())->method('getId')->willReturn($storeId);
        $rewardMock->expects($this->once())->method('setStore')->with($storeId)->willReturnSelf();
        $rewardMock->expects($this->once())->method('setAction')->with(Reward::REWARD_ACTION_REGISTER)
            ->willReturnSelf();
        $rewardMock->expects($this->once())->method('updateRewardPoints');

        $this->assertEquals(
            $customerMock,
            $this->subject->aroundCreateAccountWithPasswordHash(
                $this->accountManagementMock,
                $proceed,
                $customerMock,
                'hash'
            )
        );
    }

    public function testUpdateRewardsThrowsException()
    {
        $notificationConfig = 1;
        $websiteId = 74;
        $exception = new \Exception('Something went wrong');
        $customerMock = $this->getMock('\Magento\Customer\Model\Data\Customer', [], [], '', false);
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $proceed = function () use ($customerMock) {
            return $customerMock;
        };

        $this->rewardDataMock->expects($this->once())->method('isEnabledOnFront')->willReturn(true);
        $this->storeManagerMock->expects($this->atLeastOnce())->method('getStore')->willReturn($storeMock);
        $storeMock->expects($this->once())->method('getWebsiteId')->willReturn($websiteId);
        $this->rewardDataMock->expects($this->once())->method('getNotificationConfig')
            ->with('subscribe_by_default', $websiteId)
            ->willReturn($notificationConfig);
        $customerMock->expects($this->exactly(2))->method('setCustomAttribute')->withConsecutive(
            ['reward_update_notification', $notificationConfig],
            ['reward_warning_notification', $notificationConfig]
        );

        $this->rewardFactoryMock->expects($this->once())->method('create')->willThrowException($exception);
        $this->loggerMock->expects($this->once())->method('critical')->with($exception);

        $this->assertEquals(
            $customerMock,
            $this->subject->aroundCreateAccountWithPasswordHash(
                $this->accountManagementMock,
                $proceed,
                $customerMock,
                'hash'
            )
        );
    }
}
