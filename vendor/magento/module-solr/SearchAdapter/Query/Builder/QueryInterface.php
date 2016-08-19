<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\SearchAdapter\Query\Builder;

interface QueryInterface
{
    /**
     * @param \Solarium\QueryType\Select\Query\Query $select
     * @param \Magento\Framework\Search\Request\QueryInterface $query
     * @param string $conditionType
     * @return \Solarium\QueryType\Select\Query\Query
     */
    public function build(
        \Solarium\QueryType\Select\Query\Query $select,
        \Magento\Framework\Search\Request\QueryInterface $query,
        $conditionType
    );
}
