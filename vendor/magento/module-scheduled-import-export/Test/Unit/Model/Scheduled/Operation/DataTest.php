<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ScheduledImportExport\Test\Unit\Model\Scheduled\Operation;

use Magento\ScheduledImportExport\Model\Scheduled\Operation\Data;

/**
 * Class DataTest
 *
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data
     */
    protected $model;

    protected function setUp()
    {
        $importConfigMock = $this->getMockBuilder('Magento\ImportExport\Model\Import\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $exportConfigMock = $this->getMockBuilder('Magento\ImportExport\Model\Export\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = new \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data(
            $importConfigMock,
            $exportConfigMock
        );
    }

    /**
     * Test getServerTypesOptionArray()
     */
    public function testGetServerTypesOptionArray()
    {
        $expected = [
            Data::FILE_STORAGE => 'Local Server',
            Data::FTP_STORAGE => 'Remote FTP',
        ];
        $result = $this->model->getServerTypesOptionArray();
        $this->assertEquals($expected, $result);
    }
}
