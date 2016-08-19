<?php
/**
 * Catalog entity setup
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Setup;

use Magento\Eav\Model\Entity\Setup\Context;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class RmaSetup extends EavSetup
{
    /**
     * Retrieve default RMA item entities
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getDefaultEntities()
    {
        $entities = [
            'rma_item' => [
                'entity_model' => 'Magento\Rma\Model\ResourceModel\Item',
                'attribute_model' => 'Magento\Rma\Model\Item\Attribute',
                'table' => 'magento_rma_item_entity',
                'increment_model' => 'Magento\Eav\Model\Entity\Increment\NumericValue',
                'additional_attribute_table' => 'magento_rma_item_eav_attribute',
                'entity_attribute_collection' => null,
                'increment_per_store' => 1,
                'attributes' => [
                    'rma_entity_id' => [
                        'type' => 'static',
                        'label' => 'RMA Id',
                        'input' => 'text',
                        'required' => true,
                        'visible' => false,
                        'sort_order' => 10,
                        'position' => 10,
                    ],
                    'order_item_id' => [
                        'type' => 'static',
                        'label' => 'Order Item Id',
                        'input' => 'text',
                        'required' => true,
                        'visible' => false,
                        'sort_order' => 20,
                        'position' => 20,
                    ],
                    'qty_requested' => [
                        'type' => 'static',
                        'label' => 'Qty of requested for RMA items',
                        'input' => 'text',
                        'required' => true,
                        'visible' => false,
                        'sort_order' => 30,
                        'position' => 30,
                    ],
                    'qty_authorized' => [
                        'type' => 'static',
                        'label' => 'Qty of authorized items',
                        'input' => 'text',
                        'visible' => false,
                        'sort_order' => 40,
                        'position' => 40,
                    ],
                    'qty_approved' => [
                        'type' => 'static',
                        'label' => 'Qty of requested for RMA items',
                        'input' => 'text',
                        'visible' => false,
                        'sort_order' => 50,
                        'position' => 50,
                    ],
                    'status' => [
                        'type' => 'static',
                        'label' => 'Status',
                        'input' => 'select',
                        'source' => 'Magento\Rma\Model\Item\Attribute\Source\Status',
                        'visible' => false,
                        'sort_order' => 60,
                        'position' => 60,
                        'adminhtml_only' => 1,
                    ],
                    'product_name' => [
                        'type' => 'static',
                        'label' => 'Product Name',
                        'input' => 'text',
                        'sort_order' => 70,
                        'position' => 70,
                        'visible' => false,
                        'adminhtml_only' => 1,
                    ],
                    'product_sku' => [
                        'type' => 'static',
                        'label' => 'Product SKU',
                        'input' => 'text',
                        'sort_order' => 80,
                        'position' => 80,
                        'visible' => false,
                        'adminhtml_only' => 1,
                    ],
                    'resolution' => [
                        'type' => 'int',
                        'label' => 'Resolution',
                        'input' => 'select',
                        'sort_order' => 90,
                        'position' => 90,
                        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                        'system' => false,
                        'option' => ['values' => ['Exchange', 'Refund', 'Store Credit']],
                        'validate_rules' => 'a:0:{}',
                    ],
                    'condition' => [
                        'type' => 'int',
                        'label' => 'Item Condition',
                        'input' => 'select',
                        'sort_order' => 100,
                        'position' => 100,
                        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                        'system' => false,
                        'option' => ['values' => ['Unopened', 'Opened', 'Damaged']],
                        'validate_rules' => 'a:0:{}',
                    ],
                    'reason' => [
                        'type' => 'int',
                        'label' => 'Reason to Return',
                        'input' => 'select',
                        'sort_order' => 110,
                        'position' => 110,
                        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                        'system' => false,
                        'option' => ['values' => ['Wrong Color', 'Wrong Size', 'Out of Service']],
                        'validate_rules' => 'a:0:{}',
                    ],
                    'reason_other' => [
                        'type' => 'varchar',
                        'label' => 'Other',
                        'input' => 'text',
                        'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'sort_order' => 120,
                        'position' => 120,
                    ],
                ],
            ],
        ];
        return $entities;
    }
}
