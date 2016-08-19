<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Model\Report\Group\Data;

/**
 * General abstract class of Data Report
 */
abstract class AbstractDataGroup extends \Magento\Support\Model\Report\Group\AbstractSection
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Module\ModuleResource $resource
     * @param \Magento\Eav\Model\ConfigFactory $eavConfigFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Module\ModuleResource $resource,
        \Magento\Eav\Model\ConfigFactory $eavConfigFactory
    ) {
        $this->connection = $resource->getConnection();
        $this->eavConfig = $eavConfigFactory->create();
        parent::__construct($logger);
    }
}
