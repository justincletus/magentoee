<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Model\Report\Group\Data;

/**
 * General abstract class for Report "Duplicate Products/Categories by URL and SKU"
 */
abstract class AbstractDuplicateSection extends AbstractDataGroup
{
    /**
     * Get attribute id by attribute code
     *
     * @param string $attributeCode
     * @param int $entityTypeId
     * @return int
     */
    protected function getAttributeId($attributeCode, $entityTypeId)
    {
        $sql = 'SELECT `attribute_id`'
            . ' FROM `' . $this->connection->getTableName('eav_attribute') . '`'
            . ' WHERE `attribute_code` = "' . $attributeCode . '" AND `entity_type_id` = ' . $entityTypeId;
        return (int)$this->connection->fetchOne($sql);
    }

    /**
     * Get information about duplicates by attribute id
     *
     * @param int $attributeId
     * @param string $entityVarcharTable
     * @return array
     */
    protected function getInfoDuplicateAttributeById($attributeId, $entityVarcharTable)
    {
        $sql = 'SELECT COUNT(1) AS `cnt`, `value`'
            . ' FROM `' . $entityVarcharTable . '`'
            . ' WHERE `attribute_id` = ' . $attributeId
            . ' GROUP BY `value`'
            . ' HAVING `cnt` > 1'
            . ' ORDER BY `cnt` DESC, `entity_id`';
        return $this->connection->fetchAll($sql);
    }

    /**
     * Get information about duplicates by SKU
     *
     * @param string $entityTable
     * @return array
     */
    protected function getInfoDuplicateSku($entityTable)
    {
        $sql = 'SELECT COUNT(1) AS `cnt`, `sku`'
            . ' FROM `' . $entityTable . '`'
            . ' GROUP BY `sku`'
            . ' HAVING `cnt` > 1'
            . ' ORDER BY `cnt` DESC, `entity_id`';
        return $this->connection->fetchAll($sql);
    }
}
