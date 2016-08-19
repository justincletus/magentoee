<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Model\Actions\Condition\Product;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class AttributesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tested model
     *
     * @var \Magento\TargetRule\Model\Actions\Condition\Product\Attributes
     */
    protected $attributes;

    /**
     * Object manager helper
     *
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * Context mock
     *
     * @var \Magento\Rule\Model\Condition\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * Backend helper mock
     *
     * @var \Magento\Backend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendHelperMock;

    /**
     * Config mock
     *
     * @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * Product mock
     *
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * Product resource model mock
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceProductMock;

    /**
     * Attribute set collection mock
     *
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionMock;

    /**
     * Locale format mock
     *
     * @var \Magento\Framework\Locale\FormatInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formatInterfaceMock;

    /**
     * Editable block mock
     *
     * @var \Magento\Rule\Block\Editable|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $editableMock;

    /**
     * Product Type mock
     *
     * @var \Magento\Catalog\Model\Product\Type|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeMock;

    protected function setUp()
    {
        $this->contextMock = $this->getMock('Magento\Rule\Model\Condition\Context', [], [], '', false);
        $this->backendHelperMock = $this->getMock('Magento\Backend\Helper\Data', [], [], '', false);
        $this->configMock = $this->getMock('Magento\Eav\Model\Config', [], [], '', false);
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->collectionMock = $this->getMock(
            'Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection',
            [],
            [],
            '',
            false
        );
        $this->formatInterfaceMock = $this->getMock('\Magento\Framework\Locale\FormatInterface');
        $this->editableMock = $this->getMock('Magento\Rule\Block\Editable', [], [], '', false);
        $this->typeMock = $this->getMock('Magento\Catalog\Model\Product\Type', [], [], '', false);
        $this->resourceProductMock = $this->getMock('Magento\Catalog\Model\ResourceModel\Product', [], [], '', false);
        $this->resourceProductMock->expects($this->any())->method('loadAllAttributes')->will($this->returnSelf());
        $this->resourceProductMock->expects($this->any())->method('getAttributesByCode')->will($this->returnSelf());

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->attributes = $this->objectManagerHelper->getObject(
            'Magento\TargetRule\Model\Actions\Condition\Product\Attributes',
            [
                'context' => $this->contextMock,
                'backendData' => $this->backendHelperMock,
                'config' => $this->configMock,
                'product' => $this->productMock,
                'productResource' => $this->resourceProductMock,
                'attrSetCollection' => $this->collectionMock,
                'localeFormat' => $this->formatInterfaceMock,
                'editable' => $this->editableMock,
                'type' => $this->typeMock
            ]
        );
    }

    /**
     * Test get conditions for collection
     *
     * @param string $operator
     * @param string $whereCondition
     * @dataProvider getConditionForCollectionDataProvider
     * @return void
     */
    public function testGetConditionForCollection($operator, $whereCondition)
    {
        $this->attributes->setAttribute('category_ids');
        $this->attributes->setValueType('constant');
        $this->attributes->setValue(3);
        $this->attributes->setOperator($operator);

        $collection = null;
        $resource = $this->getMock('Magento\TargetRule\Model\ResourceModel\Index', [], [], '', false);
        $resource->expects($this->any())->method('getTable')->will($this->returnArgument(0));
        $resource->expects($this->any())->method('bindArrayOfIds')->with(3)->will($this->returnValue([3]));
        $resource->expects($this->any())->method('getOperatorCondition')
            ->with('category_id', $operator, [3])
            ->will($this->returnValue($whereCondition));

        $select = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);
        $select->expects($this->any())->method('from')->with('catalog_category_product', 'COUNT(*)')
            ->will($this->returnSelf());
        $select->expects($this->at(1))->method('where')->with('product_id=e.entity_id')->will($this->returnSelf());
        $select->expects($this->at(2))->method('where')->with($whereCondition)->will($this->returnSelf());
        $select->expects($this->any())->method('assemble')->will($this->returnValue('assembled select'));

        $object = $this->getMock('Magento\TargetRule\Model\Index', [], [], '', false);
        $object->expects($this->any())->method('getResource')->will($this->returnValue($resource));
        $object->expects($this->any())->method('select')->will($this->returnValue($select));
        $bind = [];
        $result = $this->attributes->getConditionForCollection($collection, $object, $bind);
        $this->assertEquals(
            '(assembled select) > 0',
            (string)$result
        );
    }

    /**
     * Data provider for get conditions for collection test
     *
     * @return array
     */
    public function getConditionForCollectionDataProvider()
    {
        return [
            ['==', "`category_id`='3'"],
            ['()', "`category_id` IN ('3')"],
        ];
    }
}
