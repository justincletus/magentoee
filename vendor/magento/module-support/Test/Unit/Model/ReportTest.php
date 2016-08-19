<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\Exception\LocalizedException;

class ReportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Support\Model\Report
     */
    protected $report;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Support\Model\Report\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reportConfigMock;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\Support\Model\Report\DataConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataConverterMock;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var \Magento\Support\Model\Report\Group\AbstractSection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sectionMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $timeZoneMock;

    protected function setUp()
    {
        $this->reportConfigMock = $this->getMockBuilder('Magento\Support\Model\Report\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->getMockForAbstractClass();
        $this->dataConverterMock = $this->getMockBuilder('Magento\Support\Model\Report\DataConverter')
            ->disableOriginalConstructor()
            ->getMock();
        $this->loggerMock = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->getMockForAbstractClass();
        $this->sectionMock = $this->getMockBuilder('Magento\Support\Model\Report\Group\AbstractSection')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->timeZoneMock = $this->getMock('Magento\Framework\Stdlib\DateTime\TimezoneInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->context = $this->objectManagerHelper->getObject(
            'Magento\Framework\Model\Context',
            [
                'logger' => $this->loggerMock
            ]
        );
        $this->report = $this->objectManagerHelper->getObject(
            'Magento\Support\Model\Report',
            [
                'context' => $this->context,
                'reportConfig' => $this->reportConfigMock,
                'objectManager' => $this->objectManagerMock,
                'dataConverter' => $this->dataConverterMock,
                'timeZone' => $this->timeZoneMock
            ]
        );
    }

    public function testGenerate()
    {
        $groups = ['some', 'groups', 'go', 'here'];
        $sections = ['some', 'sections', 'go', 'here'];
        $reportData = ['some' => [], 'sections' => [], 'go' => [], 'here' => []];

        $this->reportConfigMock->expects($this->once())
            ->method('getSectionNamesByGroup')
            ->with($groups)
            ->willReturn($sections);
        $this->objectManagerMock->expects($this->any())
            ->method('create')
            ->willReturn($this->sectionMock);
        $this->sectionMock->expects($this->any())
            ->method('generate')
            ->willReturn([]);

        $this->report->generate($groups);

        $this->assertEquals($groups, $this->report->getReportGroups());
        $this->assertEquals($reportData, $this->report->getReportData());
    }

    public function testPrepareReportDataNoData()
    {
        $this->assertFalse($this->report->prepareReportData());
    }

    public function testPrepareReportData()
    {
        $errorMessage = 'Something gone wrong';
        $exception = new LocalizedException(__($errorMessage));

        $reportData = [
            'section1' => [
                'title1' => [
                    'headers' => ['header1'],
                    'data' => ['data1']
                ]
            ],
            'section2' => [],
            'section3' => [
                'title3' => [
                    'data' => ['exception']
                ]
            ]
        ];

        $preparedData = [
            'section1' => [
                'title1' => [
                    'headers' => ['header1'],
                    'data' => ['data1']
                ]
            ],
            'section3' => [
                'title3' => [
                    'error' => $errorMessage
                ]
            ]
        ];

        $this->dataConverterMock->expects($this->at(0))
            ->method('prepareData')
            ->with(['headers' => ['header1'], 'data' => ['data1']])
            ->willReturn(['headers' => ['header1'], 'data' => ['data1']]);
        $this->dataConverterMock->expects($this->at(1))
            ->method('prepareData')
            ->with(['data' => ['exception']])
            ->willThrowException($exception);
        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with($exception, [])
            ->willReturn(true);

        $this->report->setReportData($reportData);
        $this->assertEquals($preparedData, $this->report->prepareReportData());
    }

    public function testGetFileNameForReportDownloadNoId()
    {
        $this->assertEquals('', $this->report->getFileNameForReportDownload());
    }

    public function testGetFileNameForReportDownload()
    {
        $date = '2015-12-03-23-45-11';
        $this->report->setId(3);
        $this->report->setClientHost('/local/host');
        $this->timeZoneMock->expects($this->once())->method('formatDateTime')->willReturn($date);

        $this->assertEquals(
            'report-2015-12-03-23-45-11_localhost.html',
            $this->report->getFileNameForReportDownload()
        );
    }
}
