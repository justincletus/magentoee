<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VisualMerchandiser\Model\Category;

use \Magento\Framework\DB\Select;

class Products
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\VisualMerchandiser\Model\Position\Cache
     */
    protected $_cache;

    /**
     * @var string
     */
    protected $_cacheKey;

    /**
     * @var \Magento\VisualMerchandiser\Model\Sorting
     */
    protected $sorting;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\VisualMerchandiser\Model\Position\Cache $cache
     * @param \Magento\VisualMerchandiser\Model\Sorting $sorting
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\VisualMerchandiser\Model\Position\Cache $cache,
        \Magento\VisualMerchandiser\Model\Sorting $sorting
    ) {
        $this->_productFactory = $productFactory;
        $this->_moduleManager = $moduleManager;
        $this->_cache = $cache;
        $this->sorting = $sorting;
    }

    /**
     * @param string $key
     * @return void
     */
    public function setCacheKey($key)
    {
        $this->_cacheKey = $key;
    }

    /**
     * @return \Magento\Catalog\Model\ProductFactory
     */
    public function getFactory()
    {
        return $this->_productFactory;
    }

    /**
     * @param int $categoryId
     * @param int $store
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getCollectionForGrid($categoryId, $store)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->getFactory()->create()
            ->getCollection()
            ->addAttributeToSelect([
                'sku',
                'name',
                'price',
                'small_image'
            ])
            ->addStoreFilter($store);

        $collection->getSelect()
            ->where('at_position.category_id = ?', $categoryId);

        if ($this->_moduleManager->isEnabled('Magento_CatalogInventory')) {
            $collection->joinField(
                'stock',
                'cataloginventory_stock_item',
                'qty',
                'product_id=entity_id',
                ['stock_id' => $this->getStockId()],
                'left'
            );
        }

        $cache = $this->_cache->getPositions($this->_cacheKey);

        if ($cache === false) {
            $collection->joinField(
                'position',
                'catalog_category_product',
                'position',
                'product_id=entity_id',
                null,
                'left'
            );
            $collection->setOrder('position', $collection::SORT_ORDER_ASC);

            // Cache the positions initially
            $_collection = clone $collection;

            $positions = [];
            $idx = 0;
            foreach ($_collection as $item) {
                $positions[$item->getId()] = $idx;
                $idx++;
            }

            $this->_cache->saveData($this->_cacheKey, $positions);
        } else {
            $collection->getSelect()
                ->reset(Select::WHERE)
                ->reset(Select::HAVING);

            $collection->addAttributeToFilter('entity_id', ['in' => array_keys($cache)]);
        }

        return $collection;
    }

    /**
     * @return int
     */
    protected function getStockId()
    {
        return \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID;
    }

    /**
     * Apply cached positions, sort order products
     * returns a base collection with WHERE IN filter applied
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function applyCachedChanges(\Magento\Catalog\Model\ResourceModel\Product\Collection $collection)
    {
        $positions = $this->_cache->getPositions($this->_cacheKey);

        if ($positions === false || count($positions) === 0) {
            return $collection;
        }

        $collection->getSelect()->reset(Select::ORDER);
        asort($positions, SORT_NUMERIC);

        $ids = implode(',', array_keys($positions));
        $field = $collection->getSelect()->getAdapter()->quoteIdentifier('e.entity_id');
        $collection->getSelect()->order(new \Zend_Db_Expr("FIELD({$field}, {$ids})"));

        $sortOrder = $this->_cache->getSortOrder($this->_cacheKey);
        $sortBuilder = $this->sorting->getSortingInstance($sortOrder);

        $sortedCollection = $sortBuilder->sort($collection);

        $idx = 0;
        $positions = [];
        foreach ($sortedCollection as $item) {
            $positions[$item->getId()] = $idx;
            $idx++;
        }

        $this->savePositionsToCache($positions);

        return $sortedCollection;
    }


    /**
     * Save products positions to cache
     *
     * @param array $positions
     * @return void
     */
    protected function savePositionsToCache($positions)
    {
        $this->_cache->saveData(
            $this->_cacheKey,
            $positions
        );
    }
}
