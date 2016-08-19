<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Model\Indexer\Category\Action;

class Full extends \Magento\CatalogPermissions\Model\Indexer\AbstractAction
{
    /**
     * Refresh entities index
     *
     * @return void
     */
    public function execute()
    {
        $this->clearIndexTempTable();

        $this->reindex();

        $this->publishCategoryIndexData();
        $this->publishProductIndexData();

        $this->removeObsoleteCategoryIndexData();
        $this->removeObsoleteProductIndexData();
        $this->cleanCache();
    }

    /**
     * Retrieve category list
     *
     * Return entity_id, path pairs.
     *
     * @return array
     */
    protected function getCategoryList()
    {
        $select = $this->connection->select()->from(
            $this->getTable('catalog_category_entity'),
            ['entity_id', 'path']
        )->order(
            'level ASC'
        );

        return $this->connection->fetchPairs($select);
    }

    /**
     * Clear all index temporary data
     *
     * @return void
     */
    protected function clearIndexTempTable()
    {
        $this->connection->delete($this->getIndexTempTable());
        $this->connection->delete($this->getProductIndexTempTable());
    }

    /**
     * Publish data from category temporary index table to index
     *
     * @return void
     */
    protected function publishCategoryIndexData()
    {
        $select = $this->connection->select()->from($this->getIndexTempTable());

        $queries = $this->prepareSelectsByRange($select, 'category_id');

        foreach ($queries as $query) {
            $this->connection->query(
                $this->connection->insertFromSelect(
                    $query,
                    $this->getIndexTable(),
                    [
                        'category_id',
                        'website_id',
                        'customer_group_id',
                        'grant_catalog_category_view',
                        'grant_catalog_product_price',
                        'grant_checkout_items'
                    ],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
                )
            );
        }
    }

    /**
     * Publish data from product temporary index table to index
     *
     * @return void
     */
    protected function publishProductIndexData()
    {
        $select = $this->connection->select()->from($this->getProductIndexTempTable());

        $queries = $this->prepareSelectsByRange($select, 'product_id', self::PRODUCT_STEP_COUNT);

        foreach ($queries as $query) {
            $this->connection->query(
                $this->connection->insertFromSelect(
                    $query,
                    $this->getProductIndexTable(),
                    [
                        'product_id',
                        'store_id',
                        'customer_group_id',
                        'grant_catalog_category_view',
                        'grant_catalog_product_price',
                        'grant_checkout_items'
                    ],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
                )
            );
        }
    }

    /**
     * Remove category unnecessary data
     *
     * @return void
     */
    protected function removeObsoleteCategoryIndexData()
    {
        $query = $this->connection->select()->from(
            ['m' => $this->getIndexTable()]
        )->joinLeft(
            ['t' => $this->getIndexTempTable()],
            'm.category_id = t.category_id' .
            ' AND m.website_id = t.website_id' .
            ' AND m.customer_group_id = t.customer_group_id'
        )->where(
            't.category_id IS NULL'
        );

        $this->connection->query($this->connection->deleteFromSelect($query, 'm'));
    }

    /**
     * Remove product unnecessary data
     *
     * @return void
     */
    protected function removeObsoleteProductIndexData()
    {
        $query = $this->connection->select()->from(
            ['m' => $this->getProductIndexTable()]
        )->joinLeft(
            ['t' => $this->getProductIndexTempTable()],
            'm.product_id = t.product_id' .
            ' AND m.store_id = t.store_id' .
            ' AND m.customer_group_id = t.customer_group_id'
        )->where(
            't.product_id IS NULL'
        );

        $this->connection->query($this->connection->deleteFromSelect($query, 'm'));
    }

    /**
     * Check whether select ranging is needed
     *
     * @return bool
     */
    protected function isRangingNeeded()
    {
        return true;
    }

    /**
     * Return list of product IDs to reindex
     *
     * @return int[]
     */
    protected function getProductList()
    {
        return [];
    }
}
