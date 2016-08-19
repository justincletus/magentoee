<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\SearchAdapter;


interface FieldMapperInterface
{
    const TYPE_QUERY = 'text';
    const TYPE_SORT = 'sort';
    const TYPE_FILTER = 'default';

    /**
     * Get field name
     *
     * @param string $attributeCode
     * @param array $context
     * @return mixed
     */
    public function getFieldName($attributeCode, $context = []);
}
