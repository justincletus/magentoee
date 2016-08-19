<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Model\Report\Group\Data;

use Magento\Catalog\Model\Product;

/**
 * Report Duplicate Products By SKU
 */
class DuplicateProductsBySkuSection extends AbstractDuplicateSection
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        return [
            (string)__('Duplicate Products By SKU') => [
                'headers' => [__('Id'), __('SKU'), __('Name')],
                'data' => $this->getDuplicateProducts()
            ]
        ];
    }

    /**
     * Get duplicate products by sku
     *
     * @return array
     */
    protected function getDuplicateProducts()
    {
        $data = [];
        try {
            $entityVarcharTable = $this->connection->getTableName('catalog_product_entity_varchar');
            $entityTable = $this->connection->getTableName('catalog_product_entity');
            $entityTypeId = $this->eavConfig->getEntityType(Product::ENTITY)->getId();
            $nameAttributeId = $this->getAttributeId('name', $entityTypeId);

            $duplicatesList = $this->getInfoDuplicateSku($entityTable);

            foreach ($duplicatesList as $duplicate) {
                $sql = 'SELECT `e`.`entity_id`, `n`.`value` AS `name`, `e`.`sku`'
                    . ' FROM `' . $entityTable . '` e'
                    . ' LEFT JOIN `' . $entityVarcharTable . '` n'
                    . ' ON `e`.`entity_id` = `n`.`entity_id` AND `n`.attribute_id = ' . $nameAttributeId
                    . ' WHERE ' . $this->connection->quoteInto('`e`.`sku` = ?', $duplicate['sku']);
                $entities = $this->connection->fetchAll($sql);

                foreach ($entities as $entity) {
                    $data[] = [
                        $entity['entity_id'],
                        $duplicate['sku'],
                        $entity['name'],
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e);
        }

        return $data;
    }
}
