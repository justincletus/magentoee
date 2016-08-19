<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCatalog\Model\ResourceModel\Product\Indexer\Eav;

/**
 * Catalog Product Eav Select and Multiply Select Attributes Indexer resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Source extends \Magento\Catalog\Model\ResourceModel\Product\Indexer\Eav\Source
{
    const TRANSIT_PREFIX = 'transit_';

    /**
     * Prepare data index for product relations
     *
     * @param array $parentIds the parent entity ids limitation
     * @return $this
     */
    protected function _prepareRelationIndex($parentIds = null)
    {
        $connection = $this->getConnection();
        $idxTable = $this->getIdxTable();

        if (!$this->tableStrategy->getUseIdxTable()) {
            $additionalIdxTable = $connection->getTableName(self::TRANSIT_PREFIX . $this->getIdxTable());
            $connection->createTemporaryTableLike($additionalIdxTable, $idxTable);

            $query = $connection->insertFromSelect(
                $this->_prepareRelationIndexSelect($parentIds),
                $additionalIdxTable,
                []
            );
            $connection->query($query);

            $select = $connection->select()->from($additionalIdxTable);
            $query = $connection->insertFromSelect(
                $select,
                $idxTable,
                [],
                \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_IGNORE
            );
            $connection->query($query);

            $connection->dropTemporaryTable($additionalIdxTable);
        } else {
            $query = $connection->insertFromSelect(
                $this->_prepareRelationIndexSelect($parentIds),
                $idxTable,
                [],
                \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_IGNORE
            );
            $connection->query($query);
        }
        return $this;
    }
}
