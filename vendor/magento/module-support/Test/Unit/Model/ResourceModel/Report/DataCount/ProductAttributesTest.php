<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\ResourceModel\Report\DataCount;

class ProductAttributesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Support\Model\ResourceModel\Report\DataCount\ProductAttributes
     */
    protected $productAttributes;

    /**
     * @var \Magento\Eav\Model\ConfigFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eavConfigFactoryMock;

    /**
     * @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eavConfigMock;

    /**
     * @var \Magento\Eav\Model\Entity\Type|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityTypeMock;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogConnectionMock;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->eavConfigFactoryMock = $this->getMock('Magento\Eav\Model\ConfigFactory', ['create'], [], '', false);
        $this->eavConfigMock = $this->getMock('Magento\Eav\Model\Config', [], [], '', false);

        $this->entityTypeMock = $this->getMock('Magento\Eav\Model\Entity\Type', [], [], '', false);
        $this->entityTypeMock = $this->getMockBuilder('Magento\Eav\Model\Entity\Type')
            ->disableOriginalConstructor()
            ->getMock();

        $this->connectionMock = $this->getMockBuilder('Magento\Framework\DB\Adapter\AdapterInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->catalogConnectionMock = $this->getMock(
            'Magento\Catalog\Model\ResourceModel\Category',
            [],
            [],
            '',
            false
        );
        $this->catalogConnectionMock->expects($this->once())->method('getConnection')->willReturn(
            $this->connectionMock
        );

        $this->productAttributes = $this->objectManagerHelper->getObject(
            'Magento\Support\Model\ResourceModel\Report\DataCount\ProductAttributes',
            [
                'eavConfigFactory' => $this->eavConfigFactoryMock,
                'catalogConnection' => $this->catalogConnectionMock
            ]
        );
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testExecute()
    {
        $expectedResult = 19740;
        $entityTypeId = 4;
        $type = 'catalog_product';

        $tableDescription = [
            'entity_id' => [
                'SCHEMA_NAME' => null,
                'TABLE_NAME' => 'catalog_product_entity',
                'COLUMN_NAME' => 'entity_id',
                'COLUMN_POSITION' => 1,
                'DATA_TYPE' => 'int',
                'DEFAULT' => null,
                'NULLABLE' => false,
                'LENGTH' => null,
                'SCALE' => null,
                'PRECISION' => null,
                'UNSIGNED' => true,
                'PRIMARY' => true,
                'PRIMARY_POSITION' => 1,
                'IDENTITY' => true
            ],
            'attribute_set_id' => [
                'SCHEMA_NAME' => null,
                'TABLE_NAME' => 'catalog_product_entity',
                'COLUMN_NAME' => 'attribute_set_id',
                'COLUMN_POSITION' => 2,
                'DATA_TYPE' => 'smallint',
                'DEFAULT' => '0',
                'NULLABLE' => false,
                'LENGTH' => null,
                'SCALE' => null,
                'PRECISION' => null,
                'UNSIGNED' => true,
                'PRIMARY' => false,
                'PRIMARY_POSITION' => null,
                'IDENTITY' => false
            ],
            'type_id' => [
                'SCHEMA_NAME' => null,
                'TABLE_NAME' => 'catalog_product_entity',
                'COLUMN_NAME' => 'type_id',
                'COLUMN_POSITION' => 3,
                'DATA_TYPE' => 'varchar',
                'DEFAULT' => 'simple',
                'NULLABLE' => false,
                'LENGTH' => '32',
                'SCALE' => null,
                'PRECISION' => null,
                'UNSIGNED' => null,
                'PRIMARY' => false,
                'PRIMARY_POSITION' => null,
                'IDENTITY' => false
            ],
            'sku' => [
                'SCHEMA_NAME' => null,
                'TABLE_NAME' => 'catalog_product_entity',
                'COLUMN_NAME' => 'sku',
                'COLUMN_POSITION' => 4,
                'DATA_TYPE' => 'varchar',
                'DEFAULT' => null,
                'NULLABLE' => true,
                'LENGTH' => '64',
                'SCALE' => null,
                'PRECISION' => null,
                'UNSIGNED' => null,
                'PRIMARY' => false,
                'PRIMARY_POSITION' => null,
                'IDENTITY' => false
            ],
            'has_options' => [
                'SCHEMA_NAME' => null,
                'TABLE_NAME' => 'catalog_product_entity',
                'COLUMN_NAME' => 'has_options',
                'COLUMN_POSITION' => 5,
                'DATA_TYPE' => 'smallint',
                'DEFAULT' => '0',
                'NULLABLE' => false,
                'LENGTH' => null,
                'SCALE' => null,
                'PRECISION' => null,
                'UNSIGNED' => null,
                'PRIMARY' => false,
                'PRIMARY_POSITION' => null,
                'IDENTITY' => false
            ],
            'required_options' => [
                'SCHEMA_NAME' => null,
                'TABLE_NAME' => 'catalog_product_entity',
                'COLUMN_NAME' => 'required_options',
                'COLUMN_POSITION' => 6,
                'DATA_TYPE' => 'smallint',
                'DEFAULT' => '0',
                'NULLABLE' => false,
                'LENGTH' => null,
                'SCALE' => null,
                'PRECISION' => null,
                'UNSIGNED' => true,
                'PRIMARY' => false,
                'PRIMARY_POSITION' => null,
                'IDENTITY' => false
            ],
            'created_at' => [
                'SCHEMA_NAME' => null,
                'TABLE_NAME' => 'catalog_product_entity',
                'COLUMN_NAME' => 'created_at',
                'COLUMN_POSITION' => 7,
                'DATA_TYPE' => 'timestamp',
                'DEFAULT' => null,
                'NULLABLE' => true,
                'LENGTH' => null,
                'SCALE' => null,
                'PRECISION' => null,
                'UNSIGNED' => null,
                'PRIMARY' => false,
                'PRIMARY_POSITION' => null,
                'IDENTITY' => false
            ],
            'updated_at' => [
                'SCHEMA_NAME' => null,
                'TABLE_NAME' => 'catalog_product_entity',
                'COLUMN_NAME' => 'updated_at',
                'COLUMN_POSITION' => 8,
                'DATA_TYPE' => 'timestamp',
                'DEFAULT' => null,
                'NULLABLE' => true,
                'LENGTH' => null,
                'SCALE' => null,
                'PRECISION' => null,
                'UNSIGNED' => null,
                'PRIMARY' => false,
                'PRIMARY_POSITION' => null,
                'IDENTITY' => false
            ]
        ];

        $info = [
            ['backend_type' => 'int'],
            ['backend_type' => 'int'],
            ['backend_type' => 'static'],
            ['backend_type' => 'int'],
            ['backend_type' => 'decimal'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'static'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'datetime'],
            ['backend_type' => 'datetime'],
            ['backend_type' => 'text'],
            ['backend_type' => 'text'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'decimal'],
            ['backend_type' => 'int'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'decimal'],
            ['backend_type' => 'decimal'],
            ['backend_type' => 'static'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'int'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'int'],
            ['backend_type' => 'int'],
            ['backend_type' => 'int'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'int'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'text'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'decimal'],
            ['backend_type' => 'decimal'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'datetime'],
            ['backend_type' => 'datetime'],
            ['backend_type' => 'int'],
            ['backend_type' => 'decimal'],
            ['backend_type' => 'decimal'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'decimal'],
            ['backend_type' => 'int'],
            ['backend_type' => 'int'],
            ['backend_type' => 'int'],
            ['backend_type' => 'int'],
            ['backend_type' => 'int'],
            ['backend_type' => 'static'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'int'],
            ['backend_type' => 'text'],
            ['backend_type' => 'static'],
            ['backend_type' => 'int'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'datetime'],
            ['backend_type' => 'decimal'],
            ['backend_type' => 'datetime'],
            ['backend_type' => 'int'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'int'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'decimal'],
            ['backend_type' => 'static'],
            ['backend_type' => 'int'],
            ['backend_type' => 'int'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'varchar'],
            ['backend_type' => 'int'],
            ['backend_type' => 'int'],
            ['backend_type' => 'int'],
            ['backend_type' => 'int'],
            ['backend_type' => 'int'],
            ['backend_type' => 'decimal'],
            ['backend_type' => 'int'],
        ];

        $this->eavConfigFactoryMock->expects($this->once())->method('create')->willReturn($this->eavConfigMock);
        $this->eavConfigMock->expects($this->once())->method('getEntityType')->with($type)->willReturn(
            $this->entityTypeMock
        );
        $this->entityTypeMock->expects($this->once())->method('getId')->willReturn($entityTypeId);

        $this->connectionMock->expects($this->atLeastOnce())->method('getTableName')->willReturnMap(
            [
                ['catalog_eav_attribute', 'catalog_eav_attribute'],
                ['eav_attribute', 'eav_attribute'],
                ['catalog_product_entity', 'catalog_product_entity']
            ]
        );
        $this->connectionMock->expects($this->once())->method('fetchAll')->willReturn($info);
        $this->connectionMock->expects($this->once())->method('describeTable')->willReturn($tableDescription);

        $this->assertSame($expectedResult, $this->productAttributes->getProductAttributesRowSizeForFlatTable());
    }
}
