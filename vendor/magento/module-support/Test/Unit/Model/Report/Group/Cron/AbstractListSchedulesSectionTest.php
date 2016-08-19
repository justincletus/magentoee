<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Group\Cron;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit_Framework_MockObject_MockObject as ObjectMock;

abstract class AbstractListSchedulesSectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory|ObjectMock
     */
    protected $scheduleCollectionFactoryMock;

    /**
     * @var \Psr\Log\LoggerInterface|ObjectMock
     */
    protected $loggerMock;

    /**
     * @var \Magento\Cron\Model\ResourceModel\Schedule\Collection|ObjectMock
     */
    protected $scheduleCollectionMock;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->scheduleCollectionFactoryMock = $this->getMock(
            'Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->loggerMock = $this->getMock('Psr\Log\LoggerInterface');
        $this->scheduleCollectionMock = $this->getMockBuilder('Magento\Cron\Model\ResourceModel\Schedule\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->scheduleCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->scheduleCollectionMock);
    }

    /**
     * @param int $id
     * @param string $jobCode
     * @param string $status
     * @param string $message
     * @param string $createdAt
     * @param string $scheduledAt
     * @param string $executedAt
     * @param string $finishedAt
     * @return \Magento\Cron\Model\Schedule|ObjectMock
     */
    protected function getScheduleMock(
        $id,
        $jobCode,
        $status,
        $message,
        $createdAt,
        $scheduledAt,
        $executedAt,
        $finishedAt
    ) {
        /** @var \Magento\Cron\Model\Schedule|ObjectMock $scheduleMock */
        $scheduleMock = $this->getMockBuilder('Magento\Cron\Model\Schedule')
            ->disableOriginalConstructor()
            ->setMethods([
                'getId', 'getJobCode', 'getCreatedAt', 'getScheduledAt',
                'getExecutedAt', 'getFinishedAt', 'getStatus', 'getMessages'
            ])
            ->getMock();
        $scheduleMock->expects($this->any())
            ->method('getId')
            ->willReturn($id);
        $scheduleMock->expects($this->any())
            ->method('getJobCode')
            ->willReturn($jobCode);
        $scheduleMock->expects($this->any())
            ->method('getStatus')
            ->willReturn($status);
        $scheduleMock->expects($this->any())
            ->method('getMessages')
            ->willReturn($message);
        $scheduleMock->expects($this->any())
            ->method('getCreatedAt')
            ->willReturn($createdAt);
        $scheduleMock->expects($this->any())
            ->method('getScheduledAt')
            ->willReturn($scheduledAt);
        $scheduleMock->expects($this->any())
            ->method('getExecutedAt')
            ->willReturn($executedAt);
        $scheduleMock->expects($this->any())
            ->method('getFinishedAt')
            ->willReturn($finishedAt);

        return $scheduleMock;
    }
}
