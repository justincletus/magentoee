<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Reward\Test\Unit\Cron;

class ScheduledPointsExpirationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $historyItemFactoryMock;

    /**
     * @var \Magento\Reward\Cron\ScheduledPointsExpiration
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->rewardDataMock = $this->getMock(
            '\Magento\Reward\Helper\Data',
            ['isEnabled', 'isEnabledOnFront', 'getGeneralConfig'],
            [],
            '',
            false
        );
        $this->historyItemFactoryMock = $this->getMock(
            '\Magento\Reward\Model\ResourceModel\Reward\HistoryFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->subject = $objectManager->getObject('Magento\Reward\Cron\ScheduledPointsExpiration',
            [
                'storeManager' => $this->storeManagerMock,
                '_historyItemFactory' => $this->historyItemFactoryMock,
                'rewardData' => $this->rewardDataMock
            ]
        );
    }

    public function testMakePointsExpiredIfRewardsDisabled()
    {
        $this->rewardDataMock->expects($this->once())->method('isEnabled')->will($this->returnValue(false));
        $this->assertEquals($this->subject, $this->subject->execute());
    }

    public function testMakePointsExpiredIfRewardsDisabledOnFront()
    {
        $websiteId = 1;

        $this->rewardDataMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));
        $this->rewardDataMock->expects($this->once())
            ->method('isEnabledOnFront')
            ->with($websiteId)
            ->will($this->returnValue(false));

        $websiteMock = $this->getMock('\Magento\Store\Model\Website', [], [], '', false);
        $websiteMock->expects($this->once())->method('getId')->will($this->returnValue($websiteId));

        $this->storeManagerMock->expects($this->once())
            ->method('getWebsites')
            ->will($this->returnValue([$websiteMock]));

        $this->assertEquals($this->subject, $this->subject->execute());
    }

    public function testMakePointsExpiredSuccess()
    {
        $websiteId = 1;
        $expireType = 'expire_type';

        $this->rewardDataMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));

        $websiteMock = $this->getMock('\Magento\Store\Model\Website', [], [], '', false);
        $websiteMock->expects($this->exactly(3))->method('getId')->will($this->returnValue($websiteId));

        $this->storeManagerMock->expects($this->once())
            ->method('getWebsites')
            ->will($this->returnValue([$websiteMock]));

        $this->rewardDataMock->expects($this->once())
            ->method('isEnabledOnFront')
            ->with($websiteId)
            ->will($this->returnValue(true));

        $this->rewardDataMock->expects($this->once())
            ->method('getGeneralConfig')
            ->with('expiry_calculation', $websiteId)
            ->will($this->returnValue($expireType));

        $rewardHistoryMock = $this->getMock('\Magento\Reward\Model\ResourceModel\Reward\History', [], [], '', false);
        $this->historyItemFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($rewardHistoryMock));

        $rewardHistoryMock->expects($this->once())
            ->method('expirePoints')
            ->with($websiteId, $expireType, 100)
            ->will($this->returnSelf());

        $this->assertEquals($this->subject, $this->subject->execute());
    }
}
