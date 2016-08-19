<?php
/**
 * Plugin for \Magento\Indexer\Model\Indexer\State model
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

class IndexerState
{
    /**
     * Related indexers IDs
     *
     * @var int[]
     */
    protected $indexerIds = [
        \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID,
        \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID,
    ];

    /**
     * Synchronize status for indexers
     *
     * @param \Magento\Framework\Indexer\StateInterface $state
     * @return \Magento\Framework\Indexer\StateInterface
     */
    public function afterSetStatus(\Magento\Framework\Indexer\StateInterface $state)
    {
        if (in_array($state->getIndexerId(), $this->indexerIds)) {
            $indexerId = ($state->getIndexerId() == \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID)
                ? \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID
                : \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID;

            $relatedState = clone $state;
            $relatedState->loadByIndexer($indexerId);
            $relatedState->setData('status', $state->getStatus());
            $relatedState->save();
        }

        return $state;
    }
}
