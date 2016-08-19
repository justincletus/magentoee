<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ResourceConnections\DB\ConnectionAdapter;

use Magento\ResourceConnections\DB\Adapter\Pdo\MysqlProxy;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\DB;
use Magento\Framework\Stdlib;
use Magento\ResourceConnections\DB\ConnectionAdapter\Mysql\Builder;

// @codingStandardsIgnoreStart
class Mysql extends \Magento\Framework\Model\ResourceModel\Type\Db\Pdo\Mysql
// @codingStandardsIgnoreEnd
{
    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * @var Mysql\Builder
     */
    protected $builder;

    /**
     * @param Stdlib\StringUtils $string
     * @param Stdlib\DateTime $dateTime
     * @param array $config
     * @param HttpRequest $request
     * @param Builder $builder
     */
    public function __construct(
        Stdlib\StringUtils $string,
        Stdlib\DateTime $dateTime,
        array $config,
        HttpRequest $request,
        Builder $builder
    ) {
        parent::__construct($string, $dateTime, $config);
        $this->request = $request;
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDbConnectionClassName()
    {
        if (isset($this->connectionConfig['slave']) && $this->request->isSafeMethod()) {
            return MysqlProxy::class;
        }
        unset($this->connectionConfig['slave']);
        return \Magento\Framework\DB\Adapter\Pdo\Mysql::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDbConnectionInstance(DB\LoggerInterface $logger)
    {
        return $this->builder->build(
            $this->getDbConnectionClassName(),
            $this->string,
            $this->dateTime,
            $logger,
            $this->connectionConfig
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection(DB\LoggerInterface $logger)
    {
        $connection = $this->getDbConnectionInstance($logger);
        if ($connection instanceof \Magento\Framework\DB\Adapter\Pdo\Mysql) {
            $profiler = $connection->getProfiler();
            if ($profiler instanceof DB\Profiler) {
                $profiler->setType($this->connectionConfig['type']);
                $profiler->setHost($this->connectionConfig['host']);
            }
        }
        return $connection;
    }
}
