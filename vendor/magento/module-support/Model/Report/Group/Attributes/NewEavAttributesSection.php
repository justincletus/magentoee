<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Model\Report\Group\Attributes;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection as AttributeCollection;

/**
 * New Eav Attributes section of Attributes report group
 */
class NewEavAttributesSection extends AbstractAttributesSection
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $attributeCollection = $this->getAttributesCollection();
        return [
            (string)__('New Eav Attributes') => $this->generateSectionData(
                $attributeCollection,
                ['entity_type_code']
            )
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function extractAttributeCollectionData(
        AttributeCollection $attributeCollection,
        array $excludedFields = []
    ) {
        $data = [];
        $existedAttributes = unserialize($this->data['existedAttributes']);
        /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
        foreach ($attributeCollection as $attribute) {
            if (!in_array($attribute->getAttributeCode(), $existedAttributes)) {
                $data[] = $this->extractAttributeData($attribute, $excludedFields);
            }
        }
        return $data;
    }
}
