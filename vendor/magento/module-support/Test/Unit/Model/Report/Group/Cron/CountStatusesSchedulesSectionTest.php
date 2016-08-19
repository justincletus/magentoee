<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Group\Cron;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit_Framework_MockObject_MockObject as ObjectMock;

class CountStatusesSchedulesSectionTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\Support\Model\Report\Group\Cron\CountStatusesSchedulesSection
     */
    protected $report;

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

        $this->report = $this->objectManagerHelper->getObject(
            'Magento\Support\Model\Report\Group\Cron\CountStatusesSchedulesSection',
            [
                'scheduleCollectionFactory' => $this->scheduleCollectionFactoryMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    /**
     * @return void
     */
    public function testGenerate()
    {
        $table = 'cron_schedule';
        $sql = "SELECT COUNT( * ) AS `cnt`, `status`
                FROM `" . $table . "`
                GROUP BY `status`
                ORDER BY `status`";
        $result = [
            ['status' => 'error', 'cnt' => 1],
            ['status' => 'pending', 'cnt' => 2],
        ];

        /** @var \Magento\Framework\DB\Adapter\AdapterInterface|ObjectMock $adapter */
        $adapter = $this->getMock('Magento\Framework\DB\Adapter\AdapterInterface');
        $adapter->expects($this->once())
            ->method('getTableName')
            ->with($table)
            ->willReturn($table);
        $adapter->expects($this->once())
            ->method('fetchAll')
            ->with($sql)
            ->willReturn($result);


        /** @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb|ObjectMock $abstractDb */
        $abstractDb = $this->getMockBuilder('Magento\Framework\Model\ResourceModel\Db\AbstractDb')
            ->setMethods(['getConnection'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractDb->expects($this->once())
            ->method('getConnection')
            ->willReturn($adapter);

        /** @var \Magento\Cron\Model\ResourceModel\Schedule\Collection|ObjectMock $collection */
        $collection = $this->getMock('Magento\Cron\Model\ResourceModel\Schedule\Collection', [], [], '', false);
        $collection->expects($this->once())
            ->method('getResource')
            ->willReturn($abstractDb);

        $this->scheduleCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collection);

        $this->setExpectedResult([['error', 1], ['pending', 2]]);
    }

    /**
     * @return void
     */
    public function testGenerateWithException()
    {
        $e = new \Exception('Test exception');
        $this->scheduleCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willThrowException($e);
        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($e);

        $this->setExpectedResult();
    }

    /**
     * @param array $data
     * @return void
     */
    protected function setExpectedResult($data = [])
    {
        $expectedResult = [
            'Cron Schedules by status code' => [
                'headers' => [__('Status Code'), __('Count')],
                'data' => $data
            ]
        ];
        $this->assertEquals($expectedResult, $this->report->generate());
    }
}
