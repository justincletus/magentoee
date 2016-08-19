<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VisualMerchandiser\Model\Rules\Rule\Collection;

/**
 * Class Fetcher
 *
 * Required for fetching collection IDs regarding filters set without reseting it`s select object JOIN parts
 *
 * @package Magento\VisualMerchandiser\Model\Rules\Rule\Collection
 */
class Fetcher
{
    /**
     * Fetch IDs from collection
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function fetchIds(\Magento\Catalog\Model\ResourceModel\Product\Collection $collection)
    {
        return $collection->getConnection()
            ->fetchCol(
                $collection->getSelect()
                    ->reset(\Magento\Framework\Db\Select::COLUMNS)
                    ->columns($collection->getEntity()->getIdFieldName())
            );
    }
}
