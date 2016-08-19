<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TargetRule\Model\Rule\Condition;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Magento\TargetRule\Model\Rule\Condition\Product\AttributesFactory
     */
    protected $_attributeFactory;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\TargetRule\Model\Rule\Condition\Product\AttributesFactory $attributesFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\TargetRule\Model\Rule\Condition\Product\AttributesFactory $attributesFactory,
        array $data = []
    ) {
        $this->_attributeFactory = $attributesFactory;
        parent::__construct($context, $data);
        $this->setType('Magento\TargetRule\Model\Rule\Condition\Combine');
    }

    /**
     * Prepare list of contitions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = [
            ['value' => $this->getType(), 'label' => __('Conditions Combination')],
            $this->_attributeFactory->create()->getNewChildSelectOptions(),
        ];

        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }

    /**
     * Collect validated attributes for Product Collection
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
