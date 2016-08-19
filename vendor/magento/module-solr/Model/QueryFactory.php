<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\Model;

class QueryFactory
{
    /**
     * @var string
     */
    private $instanceName;

    /**
     * @param string $instanceName
     */
    public function __construct(
        $instanceName = '\Solarium\QueryType\Select\Query\Query'
    ) {
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Solarium\QueryType\Select\Query\Query
     */
    public function create()
    {
        return new $this->instanceName();
    }
}
