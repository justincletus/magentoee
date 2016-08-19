<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\ScheduledImportExport\Model\Import
 */
namespace Magento\ScheduledImportExport\Test\Unit\Model;

class ImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Enterprise data import model
     *
     * @var \Magento\ScheduledImportExport\Model\Import
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_importConfigMock;

    /**
     * Init model for future tests
     */
    protected function setUp()
    {
        $this->_importConfigMock = $this->getMock('Magento\ImportExport\Model\Import\ConfigInterface');
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $indexerRegistry = $this->getMock('Magento\Framework\Indexer\IndexerRegistry', [], [], '', false);
        $this->_model = new \Magento\ScheduledImportExport\Model\Import(
            $logger,
            $this->getMock('Magento\Framework\Filesystem', [], [], '', false),
            $this->getMock('Magento\ImportExport\Helper\Data', [], [], '', false),
            $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface'),
            $this->_importConfigMock,
            $this->getMock('Magento\ImportExport\Model\Import\Entity\Factory', [], [], '', false),
            $this->getMock('Magento\ImportExport\Model\ResourceModel\Import\Data', [], [], '', false),
            $this->getMock('Magento\ImportExport\Model\Export\Adapter\CsvFactory', [], [], '', false),
            $this->getMock('\Magento\Framework\HTTP\Adapter\FileTransferFactory', [], [], '', false),
            $this->getMock('Magento\MediaStorage\Model\File\UploaderFactory', ['create'], [], '', false),
            $this->getMock('Magento\ImportExport\Model\Source\Import\Behavior\Factory', [], [], '', false),
            $indexerRegistry,
            $this->getMock('Magento\ImportExport\Model\History', [], [], '', false),
            $this->getMock('Magento\Framework\Stdlib\DateTime\DateTime', [], [], '', false)
        );
    }

    /**
     * Unset test model
     */
    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Test for method 'initialize'
     */
    public function testInitialize()
    {
        /**
         * @var $operation \Magento\ScheduledImportExport\Model\Scheduled\Operation
         */
        $operation = $this->getMock(
            'Magento\ScheduledImportExport\Model\Scheduled\Operation',
            [
                '__wakeup',
                'getFileInfo',
                'getEntityType',
                'getBehavior',
                'getOperationType',
                'getStartTime',
                'getId',
            ],
            [],
            '',
            false
        );
        $fileInfo = [
            'entity_type' => 'another customer',
            'behavior' => 'replace',
            'operation_type' => 'import',
            'custom_option' => 'value',
        ];
        $operationData = [
            'entity' => 'test entity',
            'behavior' => 'customer',
            'operation_type' => 'update',
            'run_at' => '00:00:00',
            'scheduled_operation_id' => 1,
        ];

        $operation->expects($this->once())->method('getFileInfo')->willReturn($fileInfo);
        $operation->expects($this->once())->method('getEntityType')->willReturn($operationData['entity']);
        $operation->expects($this->once())->method('getBehavior')->willReturn($operationData['behavior']);
        $operation->expects($this->once())->method('getOperationType')->willReturn($operationData['operation_type']);
        $operation->expects($this->once())->method('getStartTime')->willReturn($operationData['run_at']);
        $operation->expects($this->once())->method('getId')->willReturn($operationData['scheduled_operation_id']);

        $importMock = $this->getMock(
            '\Magento\ScheduledImportExport\Model\Import',
            [
                'setData'
            ],
            [],
            '',
            false
        );
        $expectedData = array_merge($fileInfo, $operationData);
        $importMock->expects($this->once())->method('setData')->with($expectedData);

        $actualResult = $importMock->initialize($operation);
        $this->assertEquals($importMock, $actualResult);
    }
}
