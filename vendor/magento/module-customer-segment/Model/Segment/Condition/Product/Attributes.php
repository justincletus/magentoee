<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Product;

use Zend_Db_Expr;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Attributes extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    /**
     * Used for rule property field
     *
     * @var string
     */
    protected $_isUsedForRuleProperty = 'is_used_for_promo_rules';

    /**
     * @var \Magento\CustomerSegment\Model\ResourceModel\Segment
     */
    protected $_resourceSegment;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote
     */
    protected $quoteResource;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\CustomerSegment\Model\ResourceModel\Segment $resourceSegment
     * @param \Magento\Quote\Model\ResourceModel\Quote $quoteResource
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\CustomerSegment\Model\ResourceModel\Segment $resourceSegment,
        \Magento\Quote\Model\ResourceModel\Quote $quoteResource,
        array $data = []
    ) {
        $this->_resourceSegment = $resourceSegment;
        $this->quoteResource = $quoteResource;
        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Product\Attributes');
        $this->setValue(null);
    }

    /**
     * Customize default operator input by type mapper for some types
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['numeric'] = ['==', '!=', '>=', '>', '<=', '<'];
            $this->_defaultOperatorInputByType['string'] = ['==', '!=', '{}', '!{}'];
        }
        return $this->_defaultOperatorInputByType;
    }

    /**
     * Get input type for attribute operators.
     *
     * @return string
     */
    public function getInputType()
    {
        if (!is_object($this->getAttributeObject())) {
            return 'string';
        }
        if ($this->getAttributeObject()->getAttributeCode() == 'category_ids') {
            return 'category';
        }
        $input = $this->getAttributeObject()->getFrontendInput();
        switch ($input) {
            case 'select':
            case 'multiselect':
            case 'date':
                return $input;
            default:
                return 'string';
        }
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $attributes = $this->loadAttributeOptions()->getAttributeOption();
        $conditions = [];
        foreach ($attributes as $code => $label) {
            $conditions[] = ['value' => $this->getType() . '|' . $code, 'label' => $label];
        }

        return ['value' => $conditions, 'label' => __('Product Attributes')];
    }

    /**
     * Get HTML of condition string
     *
     * @return \Magento\Framework\Phrase
     */
    public function asHtml()
    {
        return __('Product %1', parent::asHtml());
    }

    /**
     * Get product attribute object
     *
     * @return \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    public function getAttributeObject()
    {
        return $this->_config->getAttribute('catalog_product', $this->getAttribute());
    }

    /**
     * Get resource
     *
     * @return \Magento\CustomerSegment\Model\ResourceModel\Segment
     */
    public function getResource()
    {
        return $this->_resourceSegment;
    }

    /**
     * Get used subfilter type
     *
     * @return string
     */
    public function getSubfilterType()
    {
        return 'product';
    }

    /**
     * Apply product attribute subfilter to parent/base condition query
     *
     * @param string $fieldName base query field name
     * @param bool $requireValid strict validation flag
     * @param int|Zend_Db_Expr $website
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @return string
     */
    public function getSubfilterSql($fieldName, $requireValid, $website)
    {
        $attribute = $this->getAttributeObject();
        $table = $attribute->getBackendTable();

        $resource = $this->getResource();
        $select = $resource->createSelect();
        $select->from(['main' => $table], ['entity_id']);

        if ($attribute->getAttributeCode() == 'category_ids') {
            $condition = $resource->createConditionSql(
                'cat.category_id',
                $this->getOperator(),
                $this->getValueParsed()
            );
            $categorySelect = $resource->createSelect();
            $categorySelect->from(
                ['cat' => $resource->getTable('catalog_category_product')],
                'product_id'
            )->where(
                $condition
            );
            $condition = 'main.entity_id IN (' . $categorySelect . ')';
        } elseif ($attribute->isStatic()) {
            $condition = $this->getResource()->createConditionSql(
                "main.{$attribute->getAttributeCode()}",
                $this->getOperator(),
                $this->getValue()
            );
        } else {
            $select->where('main.attribute_id = ?', $attribute->getId());
            $select->join(
                ['store' => $this->getResource()->getTable('store')],
                'main.store_id=store.store_id',
                []
            )->where(
                'store.website_id IN(?)',
                [0, $website]
            );
            $condition = $this->getResource()->createConditionSql(
                'main.value',
                $this->getOperator(),
                $this->getValue()
            );
        }
        $select->where($condition);
        $inOperator = $requireValid ? 'IN' : 'NOT IN';
        if ($this->getCombineProductCondition()) {
            // when used as a child of History or List condition - "IN" always set to "IN"
            $inOperator = 'IN';
        }

        $productIds = $this->getData('product_ids');

        if ($productIds) {
            $select->where('main.entity_id IN(?)', $productIds);
        }

        $entityIds = implode(',', $this->getResource()->getConnection()->fetchCol($select));
        if (empty($entityIds)) {
            return $requireValid ? "FALSE" : "TRUE";
        }
        return sprintf("%s %s (%s)", $fieldName, $inOperator, $entityIds);
    }

    /**
     * @param int $customer
     * @param int $website
     * @param array $params
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isSatisfiedBy($customer, $website, $params)
    {
        $productId = $params['quote_item']['product_id'];
        $attribute = $this->getAttributeObject();
        $backendTable = $attribute->getBackendTable();

        $resource = $this->getResource();
        $select = $resource->createSelect();
        $select->from(['main' => $backendTable], ['entity_id']);

        if ($attribute->getAttributeCode() == 'category_ids') {
            // Ensure that given product is assigned to requested category
            $categorySelect = $resource->createSelect();
            $categorySelect->from(
                ['category' => $resource->getTable('catalog_category_product')],
                'product_id'
            )->where(
                $resource->createConditionSql('category.category_id', $this->getOperator(), $this->getValueParsed())
            );
            $condition = 'main.entity_id IN (' . $categorySelect . ')';
        } elseif ($attribute->isStatic()) {
            $condition = $resource->createConditionSql(
                "main.{$attribute->getAttributeCode()}",
                $this->getOperator(),
                $this->getValue()
            );
        } else {
            $select->where('main.attribute_id = ?', $attribute->getId());
            $select->join(
                ['store' => $resource->getTable('store')],
                'main.store_id=store.store_id',
                []
            )->where('store.website_id IN(?)', [0, $website]);
            $condition = $resource->createConditionSql('main.value', $this->getOperator(), $this->getValue());
        }
        $select->where($condition);
        $select->where('main.entity_id = ?', $productId);
        $result = $resource->getConnection()->fetchCol($select);

        return !empty($result);
    }

    /**
     * @param int $websiteId
     * @param null $requireValid
     * @return array
     */
    public function getSatisfiedIds($websiteId, $requireValid = null)
    {
        $attribute = $this->getAttributeObject();
        $table = $attribute->getBackendTable();

        $resource = $this->getResource();
        $select = $resource->createSelect();
        $select->from(['main' => $table], ['entity_id']);

        if ($attribute->getAttributeCode() == 'category_ids') {
            $condition = $resource->createConditionSql(
                'cat.category_id',
                $this->getOperator(),
                $this->getValueParsed()
            );
            $categorySelect = $resource->createSelect();
            $categorySelect->from(
                ['cat' => $resource->getTable('catalog_category_product')],
                'product_id'
            )->where(
                $condition
            );
            $condition = 'main.entity_id IN (' . $categorySelect . ')';
        } elseif ($attribute->isStatic()) {
            $condition = $this->getResource()->createConditionSql(
                "main.{$attribute->getAttributeCode()}",
                $this->getOperator(),
                $this->getValue()
            );
        } else {
            $select->where('main.attribute_id = ?', $attribute->getId());
            $select->join(
                ['store' => $this->getResource()->getTable('store')],
                'main.store_id=store.store_id',
                []
            )->where(
                'store.website_id IN(?)',
                [0, $websiteId]
            );
            $condition = $this->getResource()->createConditionSql(
                'main.value',
                $this->getOperator(),
                $this->getValue()
            );
        }
        $select->where($condition);
        $inOperator = $requireValid ? 'EXISTS' : 'NOT EXISTS';
        if ($this->getCombineProductCondition()) {
            // when used as a child of History or List condition - "EXISTS" always set to "EXISTS"
            $inOperator = 'EXISTS';
        }
        sprintf("%s (%s)", $inOperator, $select);
        $result = $this->getResource()->getConnection()->fetchCol($select);
        $quoteIds = [];
        if (!empty($result)) {
            $quoteIds = $this->executePrepareConditionSql($websiteId, $result);
        }
        return $quoteIds;
    }

    /**
     * @param int $websiteId
     * @param array $productIds
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function executePrepareConditionSql($websiteId, $productIds)
    {
        $select = $this->quoteResource->getConnection()->select();
        $select->from(
            ['item' => $this->getResource()->getTable('quote_item')],
            ['quote_id']
        );
        $conditions = "item.quote_id = list.entity_id";
        $select->joinInner(
            ['list' => $this->getResource()->getTable('quote')],
            $conditions,
            []
        );
        $select->where('list.is_active = ?', new \Zend_Db_Expr(1));
        $select->where('item.product_id IN(?)', $productIds);
        $result = $this->quoteResource->getConnection()->fetchCol($select);
        return $result;
    }

    /**
     * @param int $websiteId
     * @return array
     */
    protected function getStoreByWebsite($websiteId)
    {
        $storeTable = $this->getResource()->getTable('store');
        $storeSelect = $this->getResource()->createSelect()->from($storeTable, ['store_id'])
            ->where('website_id=?', $websiteId);
        $data = $this->getResource()->getConnection()->fetchCol($storeSelect);
        return $data;
    }
}
