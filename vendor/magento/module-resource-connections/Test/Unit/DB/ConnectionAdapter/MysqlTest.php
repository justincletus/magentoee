<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ResourceConnections\Test\Unit\DB\ConnectionAdapter;

use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ResourceConnections\DB\Adapter\Pdo\MysqlProxy;
use Magento\ResourceConnections\DB\ConnectionAdapter\Mysql\Builder;
use Magento\Framework\App\Request\Http as RequestHttp;

/**
 * Class MysqlTest
 * @package Magento\ResourceConnections\Test\Unit\DB\ConnectionAdapter
 */
class MysqlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Stdlib\StringUtils|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stringUtilsMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateTimeMock;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\ResourceConnections\DB\ConnectionAdapter\Mysql\Builder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $builderMock;

    /**
     * @var \Magento\Framework\DB\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var array
     */
    protected $configArray = [];



    public function setUp()
    {
        $this->builderMock = $this->getMock(Builder::class, ['build'], [], '', false);
        $this->dateTimeMock = $this->getMock(DateTime::class, [], [], '', false);
        $this->requestMock = $this->getMock(RequestHttp::class, ['isSafeMethod'], [], '', false);
        $this->stringUtilsMock = $this->getMock(StringUtils::class, [], [], '', false);
        $this->loggerMock = $this->getMock(LoggerInterface::class, [], [], '', false);
    }

    /**
     * Test that real adapter is returned for non-safe method
     */
    public function testInstantiationForNonSafeMethodWithoutSlave()
    {
        $config = [
            'host' => 'testHost',
            'active' => true,
            'initStatements' => 'SET NAMES utf8',
            'type' => 'pdo_mysql'
        ];
        $this->requestMock->expects($this->never())->method('isSafeMethod')->will($this->returnValue(false));
        $this->builderMock->expects($this->once())->method('build')->with(
            \Magento\Framework\DB\Adapter\Pdo\Mysql::class,
            $this->stringUtilsMock,
            $this->dateTimeMock,
            $this->loggerMock,
            $config
        );
        $connectionAdapter = new \Magento\ResourceConnections\DB\ConnectionAdapter\Mysql(
            $this->stringUtilsMock,
            $this->dateTimeMock,
            $config,
            $this->requestMock,
            $this->builderMock
        );
        $connectionAdapter->getConnection($this->loggerMock);
    }

    /**
     * Test that real adapter is returned for non-safe method even if slave is set
     */
    public function testInstantiationForSafeMethodWithSlave()
    {
        $config = [
            'host' => 'testHost',
            'active' => true,
            'initStatements' => 'SET NAMES utf8',
            'type' => 'pdo_mysql',
            'slave' => [
                'host' => 'slaveHost'
            ]
        ];
        $expectedBuildConfig = $config;
        unset($expectedBuildConfig['slave']);
        $this->requestMock->expects($this->once())->method('isSafeMethod')->will($this->returnValue(false));
        $this->builderMock->expects($this->once())->method('build')->with(
            \Magento\Framework\DB\Adapter\Pdo\Mysql::class,
            $this->stringUtilsMock,
            $this->dateTimeMock,
            $this->loggerMock,
            $expectedBuildConfig
        );
        $connectionAdapter = new \Magento\ResourceConnections\DB\ConnectionAdapter\Mysql(
            $this->stringUtilsMock,
            $this->dateTimeMock,
            $config,
            $this->requestMock,
            $this->builderMock
        );
        $connectionAdapter->getConnection($this->loggerMock);
    }

    /**
     * Test that real adapter is returned for safe method if slave is not set
     */
    public function testInstantiationForSafeRequestWithoutSlave()
    {
        $config = [
            'host' => 'testHost',
            'active' => true,
            'initStatements' => 'SET NAMES utf8',
            'type' => 'pdo_mysql',
        ];
        $this->requestMock->expects($this->never())->method('isSafeMethod');
        $this->builderMock->expects($this->once())->method('build')->with(
            \Magento\Framework\DB\Adapter\Pdo\Mysql::class,
            $this->stringUtilsMock,
            $this->dateTimeMock,
            $this->loggerMock,
            $config
        );
        $connectionAdapter = new \Magento\ResourceConnections\DB\ConnectionAdapter\Mysql(
            $this->stringUtilsMock,
            $this->dateTimeMock,
            $config,
            $this->requestMock,
            $this->builderMock
        );
        $connectionAdapter->getConnection($this->loggerMock);
    }

    /**
     * Test that adapter proxy is returned for safe method if slave config is set
     */
    public function testInstantiationForSafeRequestWithSlave()
    {
        $config = [
            'host' => 'testHost',
            'active' => true,
            'initStatements' => 'SET NAMES utf8',
            'type' => 'pdo_mysql',
            'slave' => [
                'host' => 'slaveHost'
            ]
        ];
        $this->requestMock->expects($this->once())->method('isSafeMethod')->will($this->returnValue(true));
        $this->builderMock->expects($this->once())->method('build')->with(
            MysqlProxy::class,
            $this->stringUtilsMock,
            $this->dateTimeMock,
            $this->loggerMock,
            $config
        );
        $connectionAdapter = new \Magento\ResourceConnections\DB\ConnectionAdapter\Mysql(
            $this->stringUtilsMock,
            $this->dateTimeMock,
            $config,
            $this->requestMock,
            $this->builderMock
        );
        $connectionAdapter->getConnection($this->loggerMock);
    }
}
