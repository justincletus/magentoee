<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ForeignKey\Test\Unit\Config;

use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\ForeignKey\Config\Reader;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Model\ResourceModel\Type\Db\ConnectionFactory
     */
    protected $connectionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\DeploymentConfig
     */
    protected $deploymentConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\ForeignKey\Config\Processor
     */
    protected $processorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dbReaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connectionMock;


    protected function setUp()
    {
        $this->deploymentConfig = $this->getMock('Magento\Framework\App\DeploymentConfig', [], [], '', false);
        $this->connectionFactoryMock =
            $this->getMock('Magento\Framework\Model\ResourceModel\Type\Db\ConnectionFactory', [], [], '', false);
        $this->processorMock = $this->getMock('Magento\Framework\ForeignKey\Config\Processor', [], [], '', false);
        $this->connectionMock = $this->getMock('Magento\Framework\DB\Adapter\AdapterInterface');
        $this->dbReaderMock = $this->getMock('Magento\Framework\ForeignKey\Config\DbReader', [], [], '', false);
        $this->reader = new \Magento\Framework\ForeignKey\Config\Reader(
            $this->getMock('Magento\Framework\Config\FileResolverInterface'),
            $this->getMock('Magento\Framework\ForeignKey\Config\Converter', [], [], '', false),
            $this->getMock('Magento\Framework\ForeignKey\Config\SchemaLocator', [], [], '', false),
            $this->getMock('Magento\Framework\Config\ValidationStateInterface'),
            $this->connectionFactoryMock,
            $this->deploymentConfig,
            $this->processorMock,
            $this->dbReaderMock
        );
    }

    public function testRead()
    {
        $connectionConfig['default'] = [
            'host' => 'localhost',
            'dbname' => 'example',
            'username' => 'root',
            'password' => '',
            'model' => 'mysql4',
            'initStatements' => 'SET NAMES utf8;',
            'active' => 1,
        ];
        $tables = ['prefix_prefix_table'];
        $databaseTables['prefix_table'] = [
            'name' => 'prefix_table',
            'prefixed_name' => 'prefix_prefix_table',
            'connection' => 'default',
        ];
        $this->deploymentConfig
            ->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap(
                [
                    [ConfigOptionsListConstants::CONFIG_PATH_DB_PREFIX, null, 'prefix_'],
                    [ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTIONS, null, $connectionConfig]
                ]
            ));
        $this->connectionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with($connectionConfig['default'])
            ->willReturn($this->connectionMock);
        $this->connectionMock->expects($this->once())->method('getTables')->willReturn($tables);
        $databaseConstraints = [];
        $this->dbReaderMock->expects($this->once())->method('read')->willReturn($databaseConstraints);
        $this->processorMock->expects($this->once())
            ->method('process')
            ->with([], $databaseConstraints, $databaseTables);
        $this->reader->read();
    }
}
