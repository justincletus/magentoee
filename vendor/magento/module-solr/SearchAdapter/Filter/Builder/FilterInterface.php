<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\SearchAdapter\Filter\Builder;

use Magento\Framework\Search\Request\FilterInterface as RequestFilterInterface;

interface FilterInterface
{
    const NEGATION_OPERATOR = '-';

    /**
     * @param RequestFilterInterface $filter
     * @return string
     */
    public function buildFilter(RequestFilterInterface $filter);
}
