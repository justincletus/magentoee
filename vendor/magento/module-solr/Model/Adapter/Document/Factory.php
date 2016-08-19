<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\Model\Adapter\Document;

class Factory
{
    /**
     * Document class
     *
     * @var string
     */
    protected $instanceName;

    /**
     * @param string $instanceName
     */
    public function __construct(
        $instanceName = 'Solarium\QueryType\Update\Query\Document\Document'
    ) {
        $this->instanceName = $instanceName;
    }

    /**
     * @return \Solarium\QueryType\Update\Query\Document\Document
     */
    public function create()
    {
        return new $this->instanceName();
    }
}
