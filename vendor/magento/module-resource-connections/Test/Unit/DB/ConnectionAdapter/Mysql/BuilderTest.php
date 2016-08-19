<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ResourceConnections\Test\Unit\DB\ConnectionAdapter\Mysql;

use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ResourceConnections\DB\ConnectionAdapter\Mysql\Builder;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ResourceConnections\DB\ConnectionAdapter\Mysql\Builder
     */
    protected $builder;

    /**
     * Test set up
     */
    protected function setUp()
    {
        $this->builder = new Builder();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage
     * Invalid instance creation attempt. Class must extend Magento\Framework\DB\Adapter\Pdo\Mysql
     */
    public function testCreateNonConnectionInstance()
    {
        $string = $this->getMock(StringUtils::class, [], [], '', false);
        $dateTime = $this->getMock(DateTime::class, [], [], '', false);
        $logger = $this->getMock(LoggerInterface::class, [], [], '', false);
        $this->builder->build(
            \Magento\ResourceConnections\DB\Select::class,
            $string,
            $dateTime,
            $logger,
            []
        );
    }
}
