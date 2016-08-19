<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Model\Report\Group\Data;

use Magento\Catalog\Model\Category;

/**
 * Report Duplicate Categories By URL Key
 */
class DuplicateCategoriesByUrlSection extends AbstractDuplicateSection
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        return [
            (string)__('Duplicate Categories By URL Key') => [
                'headers' => [__('Id'), __('URL key'), __('Name'), __('Store')],
                'data' => $this->getDuplicateCategories()
            ]
        ];
    }

    /**
     * Get duplicate categories by url key
     *
     * @return array
     */
    protected function getDuplicateCategories()
    {
        $data = [];
        try {
            $entityVarcharTable = $this->connection->getTableName('catalog_category_entity_varchar');
            $storeTable = $this->connection->getTableName('store');
            $entityTypeId = $this->eavConfig->getEntityType(Category::ENTITY)->getId();
            $nameAttributeId = $this->getAttributeId('name', $entityTypeId);
            $urlKeyAttributeId = $this->getAttributeId('url_key', $entityTypeId);

            $duplicatesList = $this->getInfoDuplicateAttributeById($urlKeyAttributeId, $entityVarcharTable);

            foreach ($duplicatesList as $duplicate) {
                $sql = 'SELECT `u`.`entity_id`, `n`.`value` AS `name`, `u`.`store_id`, `s`.`name` AS `store_name`'
                    . ' FROM `' . $entityVarcharTable . '` u'
                    . ' LEFT JOIN `' . $storeTable . '` s ON `u`.`store_id` = `s`.`store_id`'
                    . ' LEFT JOIN `' . $entityVarcharTable . '` n ON `u`.`entity_id` = `n`.`entity_id` AND'
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
                        $entity['store_name'] . ' {ID:' . $entity['store_id'] . '}'
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e);
        }

        return $data;
    }
}
