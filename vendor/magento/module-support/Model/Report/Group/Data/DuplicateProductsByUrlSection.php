<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Model\Report\Group\Data;

use Magento\Catalog\Model\Product;

/**
 * Report Duplicate Products By URL Key
 */
class DuplicateProductsByUrlSection extends AbstractDuplicateSection
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        return [
            (string)__('Duplicate Products By URL Key') => [
                'headers' => [__('Id'), __('URL Key'), __('Name'), __('Website'), __('Store')],
                'data' => $this->getDuplicateProducts()
            ]
        ];
    }

    /**
     * Get duplicate products by url key
     *
     * @return array
     */
    protected function getDuplicateProducts()
    {
        $data = [];
        try {
            $entityVarcharTable = $this->connection->getTableName('catalog_product_entity_varchar');
            $productWebsiteTable = $this->connection->getTableName('catalog_product_website');
            $storeWebsiteTable = $this->connection->getTableName('store_website');
            $storeTable = $this->connection->getTableName('store');
            $entityTypeId = $this->eavConfig->getEntityType(Product::ENTITY)->getId();
            $nameAttributeId = $this->getAttributeId('name', $entityTypeId);
            $urlKeyAttributeId = $this->getAttributeId('url_key', $entityTypeId);

            $duplicatesList = $this->getInfoDuplicateAttributeById($urlKeyAttributeId, $entityVarcharTable);

            foreach ($duplicatesList as $duplicate) {
                $sql = 'SELECT `u`.`entity_id`, `n`.`value` AS `name`, `u`.`store_id`, `s`.`name` AS `store_name`,'
                    . ' `pw`.`website_id`, `sw`.`name` AS `website_name`'
                    . ' FROM `' . $entityVarcharTable . '` AS `u`'
                    . ' LEFT JOIN `' . $productWebsiteTable . '` AS `pw` ON `u`.`entity_id` = `pw`.`product_id`'
                    . ' LEFT JOIN `' . $storeWebsiteTable . '` AS `sw` ON `pw`.`website_id` = `sw`.`website_id`'
                    . ' LEFT JOIN `' . $storeTable . '` s ON `u`.`store_id` = `s`.`store_id`'
                    . ' LEFT JOIN `' . $entityVarcharTable . '` AS `n` ON `u`.`entity_id` = `n`.`entity_id` AND'
                    . ' `u`.`store_id` = `n`.`store_id` AND'
                    . ' `n`.attribute_id = ' . $nameAttributeId
                    . ' WHERE `u`.`attribute_id` = ' . $urlKeyAttributeId
                    . ' AND ' . $this->connection->quoteInto('`u`.`value` = ?', $duplicate['value']);
                $entities = $this->connection->fetchAll($sql);

                foreach ($entities as $entity) {
                    $data[] = [
                        $entity['entity_id'],
                        $duplicate['value'],
                        $entity['name'],
                        $entity['website_id']
                            ? $entity['website_name'] . ' {ID:' . $entity['website_id'] . '}' : 'Not select',
                        $entity['store_id']
                            ? $entity['store_name'] . ' {ID:' . $entity['store_id'] . '}' : 'All'
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e);
        }

        return $data;
    }
}
