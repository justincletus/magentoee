<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\ForeignKey\Test\Unit\Config;

use Magento\Framework\ForeignKey\Config\DbReader;

class DbReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DbReader
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $deploymentConfig;

    protected function setUp()
    {
        $this->connectionFactoryMock = $this->getMock(
            'Magento\Framework\Model\ResourceModel\Type\Db\ConnectionFactory',
            [],
            [],
            '',
            false
        );
        $this->deploymentConfig = $this->getMock('Magento\Framework\App\DeploymentConfig', [], [], '', false);
        $this->model = new DbReader($this->connectionFactoryMock, $this->deploymentConfig);
    }

    public function testRead()
    {
        $dbConfig = [
            'default' => [
                'host' => '127.0.0.1',
                'dbname' => 'magento',
                'username' => 'root',
                'password' => 'root',
                'model' => 'mysql4',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1',
            ]
        ];
        $this->deploymentConfig->expects($this->once())->method('get')->with('db/connection')->willReturn($dbConfig);
        $connection = $this->getMock('\Magento\Framework\DB\Adapter\AdapterInterface');
        $this->connectionFactoryMock->expects($this->once())
            ->method('create')
            ->with($dbConfig['default'])
            ->willReturn($connection);

        $selectMock = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);

        $connection->expects($this->once())->method('select')->willReturn($selectMock);
        $selectMock->expects($this->once())
            ->method('from')
            ->with(['info' => 'information_schema.KEY_COLUMN_USAGE'], [])
            ->willReturnSelf();

        $selectMock->expects($this->once())
            ->method('joinInner')
            ->with(
                ['constraints' => 'information_schema.REFERENTIAL_CONSTRAINTS'],
                'constraints.CONSTRAINT_NAME = info.CONSTRAINT_NAME'
                . ' AND constraints.CONSTRAINT_SCHEMA = info.CONSTRAINT_SCHEMA',
                []
            )->willReturnSelf();

        $selectMock->expects($this->any())
            ->method('where')
            ->willReturnSelf();

        $selectMock->expects($this->once())
            ->method('columns')
            ->with(
                [
                    'name' => 'constraints.CONSTRAINT_NAME',
                    'table_name' => 'info.TABLE_NAME',
                    'reference_table_name' => 'info.REFERENCED_TABLE_NAME',
                    'field_name' => 'info.COLUMN_NAME',
                    'reference_field_name' => 'info.REFERENCED_COLUMN_NAME',
                    'delete_strategy' => 'constraints.DELETE_RULE'
                ]
            )->willReturnSelf();

        $data = [
            [
                'delete_strategy' => 'CASCADE',
                'table_name' => 'some_table',
                'reference_table_name' => 'some_table_two',
                'field_name' => 'field_one',
                'reference_field_name' => 'field_two'
            ]
        ];
        $connection->expects($this->once())->method('fetchAssoc')->with($selectMock)->willReturn($data);

        $expected = [
            [
                'delete_strategy' => 'DB CASCADE',
                'table_name' => 'some_table',
                'reference_table_name' => 'some_table_two',
                'field_name' => 'field_one',
                'reference_field_name' => 'field_two',
                'connection' => 'default',
                'reference_connection' => 'default'
            ]
        ];
        $actual = $this->model->read();
        $this->assertEquals($expected, array_values($actual));
    }
}
