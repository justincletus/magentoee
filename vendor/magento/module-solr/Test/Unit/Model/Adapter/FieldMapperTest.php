<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Solr\Test\Unit\Model\Adapter;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Solr\SearchAdapter\FieldMapperInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class FieldMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Solr\Model\Adapter\FieldMapper
     */
    protected $mapper;

    /**
     * @var \Magento\Eav\Model\Config|MockObject
     */
    protected $eavConfig;

    protected function setUp()
    {
        $this->eavConfig = $this->getMockBuilder('\Magento\Eav\Model\Config')
            ->disableOriginalConstructor()
            ->setMethods(['getEntityType', 'getAttribute'])
            ->getMock();

        $objectManager = new ObjectManagerHelper($this);

        $this->mapper = $objectManager->getObject(
            '\Magento\Solr\Model\Adapter\FieldMapper',
            [
                'eavConfig' => $this->eavConfig,
            ]
        );
    }

    /**
     * @dataProvider attributeCodeProvider
     * @param $attributeCode
     * @param $fieldName
     * @param array $context
     */
    public function testGetFieldName($attributeCode, $fieldName, $context = [])
    {
        $attribute = $this->getMockBuilder('\Magento\Catalog\Model\ResourceModel\Eav\Attribute')
            ->disableOriginalConstructor()
            ->getMock();

        $this->eavConfig->expects($this->any())->method('getAttribute')
            ->with(ProductAttributeInterface::ENTITY_TYPE_CODE, $attributeCode)
            ->willReturn($attribute);

        $this->assertEquals(
            $fieldName,
            $this->mapper->getFieldName($attributeCode, $context)
        );
    }

    /**
     * @return array
     */
    public static function attributeCodeProvider()
    {
        return [
            ['id', 'id'],
            ['price', 'price_22_66', ['customerGroupId' => '22', 'websiteId' => '66']],
            ['position', 'position_category_33', ['categoryId' => '33']],
            ['test_code', 'attr_test_code_def', ['type' => 'text']],
            ['test_code', 'attr_test_code_nb', ['type' => 'text', 'localeCode' => 'nn_NO']],
            ['test_code', 'attr_test_code_ru', ['type' => 'text', 'localeCode' => 'ru_RU']],
            ['*', 'fulltext_ru', ['type' => 'text', 'localeCode' => 'ru_RU']],
            ['test_code', 'attr_sort_string_test_code', ['type' => 'value']],
            ['spell', 'attr_spell_def', ['type' => 'text']],
            ['fulltext', 'attr_fulltext_def', ['type' => 'text']],
            ['filter', 'attr_filter_def', ['type' => 'default']],
            ['filter', 'attr_filter_def', ['type' => FieldMapperInterface::TYPE_FILTER]],
        ];
    }
}
