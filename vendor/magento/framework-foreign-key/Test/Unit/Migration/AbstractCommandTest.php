<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\ForeignKey\Test\Unit\Migration;

use Magento\Framework\Config\File\ConfigFilePool as Config;

class AbstractCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ForeignKey\Migration\AbstractCommand
     */
    protected $command;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $readerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $writerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $factoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Console\Input\InputInterface
     */
    protected $inputMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Console\Output\OutputInterface
     */
    protected $outputMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $host;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $connection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $defaultConnection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $newConnection;

    protected function setUp()
    {
        $this->readerMock = $this->getMock('Magento\Framework\App\DeploymentConfig\Reader', [], [], '', false, false);
        $this->writerMock = $this->getMock('Magento\Framework\App\DeploymentConfig\Writer', [], [], '', false, false);
        $this->factoryMock = $this->getMock(
            'Magento\Framework\App\ResourceConnection\ConnectionFactory',
            [],
            [],
            '',
            false,
            false
        );
        $this->inputMock = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $this->outputMock = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $definitions = $this->getDefinitions(['host', 'connection', 'resource', 'username', 'password', 'dbname']);

        $this->command = $this->getMockForAbstractClass(
            'Magento\Framework\ForeignKey\Migration\AbstractCommand',
            [],
            '',
            false,
            false,
            true,
            ['getCommandName', 'getCommandDescription', 'getCommandDefinition'],
            false
        );
        $this->command->expects($this->once())->method('getCommandName')->willReturn('setup:db-schema:split');
        $this->command->expects($this->once())->method('getCommandDescription')->willReturn('description');
        $this->command->expects($this->once())->method('getCommandDefinition')->willReturn($definitions);
        $this->defaultConnection = $this->getMock('Magento\Framework\DB\Adapter\Pdo\Mysql', [], [], '', false, false);
        $this->newConnection = $this->getMock('Magento\Framework\DB\Adapter\Pdo\Mysql', [], [], '', false, false);
    }

    protected function getDefinitions($names)
    {
        $definitions = [];

        foreach ($names as $name) {
            $definition = $this->getMock('Symfony\Component\Console\Input\InputOption', [], [], '', false, false);
            $definition->expects($this->atLeastOnce())->method('getName')->willReturn($name);
            $definitions[] = $definition;
        }

        return $definitions;
    }

    /**
     * @dataProvider executeProvider
     * @param $config
     * @param $newConfig
     * @param $tablesToMove
     * @throws \Exception
     */
    public function testExecute($config, $newConnectionConfig, $existingTables, $tablesToMove)
    {
        $reflectedClass = new \ReflectionClass('Magento\Framework\ForeignKey\Migration\AbstractCommand');
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke(
            $this->command,
            $this->writerMock,
            $this->readerMock,
            $this->factoryMock,
            array_keys($tablesToMove),
            null
        );
        $tablesToMoveWithPrefix = [];
        foreach ($tablesToMove as $tableName => $foreignKeys) {
            $tablesToMoveWithPrefix[$config['db']['table_prefix'] . $tableName] = $foreignKeys;
        }

        $optionMap = [
            ['host', 'test_host'],
            ['connection', 'new_connection'],
            ['dbname', 'new_db'],
            ['username', 'new_user_name'],
            ['password', 'new_user_password'],
            ['resource', 'new_resource'],
        ];
        $this->inputMock->expects($this->any())->method('getOption')->willReturnMap($optionMap);
        $this->readerMock->expects($this->once())->method('load')->with(Config::APP_ENV)->willReturn($config);
        $this->factoryMock->expects($this->at(0))
            ->method('create')
            ->with($config['db']['connection']['default'])
            ->willReturn($this->defaultConnection);

        $this->factoryMock->expects($this->at(1))
            ->method('create')
            ->with($newConnectionConfig)
            ->willReturn($this->newConnection);

        $updatedConfig = $this->getUpdatedConfig($config, $newConnectionConfig);

        $movedTables = array_intersect(array_keys($tablesToMoveWithPrefix), $existingTables);
        $remainingTables = array_diff($existingTables, array_keys($tablesToMoveWithPrefix));

        $this->writerMock->expects($this->once())->method('saveConfig')->with($updatedConfig, true);
        $this->outputMock->expects($this->once())->method('writeln')->with('Migration has been finished successfully!');

        foreach ($tablesToMoveWithPrefix as $tableName => $foreignKeys) {
            $tableExists = in_array($tableName, $existingTables);
            $this->defaultConnection->method('isTableExists')
                ->with($tableName)
                ->willReturn($tableExists);
            if ($tableExists) {
                $data = ['id' => 10, 'name' => 'test'];
                $this->moveTableAsserts($tableName, $data);

                $this->newConnection->expects($this->any())
                    ->method('getForeignKeys')
                    ->with($tableName)
                    ->willReturn($foreignKeys);
            }
        }
        foreach ($remainingTables as $tableName) {
            $this->defaultConnection->expects($this->any())
                ->method('getForeignKeys')
                ->with($tableName)
                ->willReturn([]);
        }

        $this->newConnection->expects($this->any())
            ->method('getTables')
            ->willReturn($movedTables);
        $this->defaultConnection->expects($this->any())
            ->method('getTables')
            ->willReturn($remainingTables);

        $this->command->run($this->inputMock, $this->outputMock);
    }

    protected function moveTableAsserts($tableName, $data)
    {
        $select = $this->getMock('Magento\Framework\DB\Select', [], [], '', false, false);
        $stmt = $this->getMock('Zend_Db_Statement_Pdo', [], [], '', false, false);
        $this->defaultConnection->expects($this->once())->method('select')->willReturn($select);
        $select->expects($this->once())->method('from')->with($tableName)->willReturnSelf();
        $this->defaultConnection->expects($this->any())->method('query')->willReturn($stmt);
        $stmt->expects($this->once())->method('fetchAll')->willReturn([$data]);
        $this->newConnection->expects($this->once())
            ->method('insertArray')
            ->with($tableName, array_keys($data), [$data]);
        $this->defaultConnection->expects($this->once())->method('dropTable')->with($tableName);
    }

    protected function getUpdatedConfig($config, $newConfig)
    {
        $updatedConfig = [Config::APP_ENV => $config];
        $updatedConfig[Config::APP_ENV]['db']['connection']['new_connection'] = $newConfig;
        $updatedConfig[Config::APP_ENV]['resource']['new_resource']['connection'] = 'new_connection';
        return $updatedConfig;
    }

    public function executeProvider()
    {
        $config = [
            'db' => [
                'table_prefix' => 'prefix_',
                'connection' => [
                    'default' => [
                        'host' => 'localhost',
                        'dbname' => 'test',
                        'username' => 'test',
                        'password' => '123123q',
                        'model' => 'mysql4',
                        'engine' => 'innodb',
                        'initStatements' => 'SET NAMES utf8;',
                        'active' => '1'
                    ]
                ]
            ]
        ];
        $newConnectionConfig = [
            'host' => 'test_host',
            'dbname' => 'new_db',
            'username' => 'new_user_name',
            'password' => 'new_user_password',
            'model' => 'mysql4',
            'engine' => 'innodb',
            'initStatements' => 'SET NAMES utf8;',
            'active' => '1'
        ];
        $foreignKeys = [
            [
                'REF_TABLE_NAME' => 'some_table',
                'TABLE_NAME' => 'prefix_test_table_1',
                'FK_NAME' => 'test_fk_name'
            ]
        ];

        return [
            [$config, $newConnectionConfig, [], ['test_table_1' => []]],
            [$config, $newConnectionConfig, ['prefix_test_table_1'], ['test_table_1' => []]],
            [$config, $newConnectionConfig, ['prefix_test_table_1', 'second'], ['test_table_1' => []]],
            [$config, $newConnectionConfig, ['prefix_test_table_1'], ['test_table_1' => $foreignKeys]]
        ];
    }
}
