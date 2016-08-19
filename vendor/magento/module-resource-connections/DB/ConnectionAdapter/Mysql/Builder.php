<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ResourceConnections\DB\ConnectionAdapter\Mysql;

use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\DB\Adapter\Pdo\Mysql;

class Builder
{
    /**
     * Build connection instance
     *
     * @param string $instanceName
     * @param array $config
     * @param LoggerInterface $logger
     * @param StringUtils $stringUtils
     * @param DateTime $dateTime
     * @return Mysql
     */
    public function build(
        $instanceName,
        StringUtils $stringUtils,
        DateTime $dateTime,
        LoggerInterface $logger,
        array $config
    ) {
        if (!in_array(Mysql::class, class_parents($instanceName, true) + [$instanceName => $instanceName])) {
            throw new \InvalidArgumentException('Invalid instance creation attempt. Class must extend ' . Mysql::class);
        }
        return new $instanceName($stringUtils, $dateTime, $logger, $config, $this);
    }
}
