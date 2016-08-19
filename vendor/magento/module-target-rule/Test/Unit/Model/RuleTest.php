<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Model;

use Magento\Catalog\Model\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class RuleTest
 * @package Magento\TargetRule\Model
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tested model
     *
     * @var \Magento\TargetRule\Model\Rule
     */
    protected $_rule;

    /**
     * SQL Builder mock
     *
     * @var \Magento\Rule\Model\Condition\Sql\Builder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sqlBuilderMock;

    /**
     * Product factory mock
     *
     * @var \Magento\Catalog\Model\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productFactory;

    /**
     * Rule factory mock
     *
     * @var \Magento\TargetRule\Model\Rule\Condition\CombineFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ruleFactory;

    /**
     * Action factory mock
     *
     * @var \Magento\TargetRule\Model\Actions\Condition\CombineFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_actionFactory;

    /**
     * Locale date mock
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_localeDate;

    public function setUp()
    {
        $this->_sqlBuilderMock = $this->_getCleanMock('\Magento\Rule\Model\Condition\Sql\Builder');

        $this->_productFactory = $this->getMock('\Magento\Catalog\Model\ProductFactory', ['create'], [], '', false);

        $this->_ruleFactory = $this->getMock(
            '\Magento\TargetRule\Model\Rule\Condition\CombineFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->_actionFactory = $this->getMock(
            '\Magento\TargetRule\Model\Actions\Condition\CombineFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->_localeDate = $this->getMockForAbstractClass(
            '\Magento\Framework\Stdlib\DateTime\TimezoneInterface',
            ['isScopeDateInInterval'],
            '',
            false
        );

        $this->_rule = (new ObjectManager($this))->getObject(
            'Magento\TargetRule\Model\Rule',
            [
                'context' => $this->_getCleanMock('\Magento\Framework\Model\Context'),
                'registry' => $this->_getCleanMock('\Magento\Framework\Registry'),
                'formFactory' => $this->_getCleanMock('\Magento\Framework\Data\FormFactory'),
                'localeDate' => $this->_localeDate,
                'ruleFactory' => $this->_ruleFactory,
                'actionFactory' => $this->_actionFactory,
                'productFactory' => $this->_productFactory,
                'ruleProductIndexerProcessor' => $this->_getCleanMock(
                    '\Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor'
                ),
                'sqlBuilder' => $this->_sqlBuilderMock,
            ]
        );
    }

    /**
     * Get clean mock by class name
     *
     * @param string $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getCleanMock($className)
    {
        return $this->getMock($className, [], [], '', false);
    }

    public function testDataHasChangedForAny()
    {
        $fields = ['first', 'second'];
        $this->assertEquals(false, $this->_rule->dataHasChangedForAny($fields));

        $fields = ['first', 'second'];
        $this->_rule->setData('first', 'test data');
        $this->_rule->setOrigData('first', 'origin test data');
        $this->assertEquals(true, $this->_rule->dataHasChangedForAny($fields));
    }

    public function testGetConditionsInstance()
    {
        $this->_ruleFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue(true));

        $this->assertEquals(true, $this->_rule->getConditionsInstance());
    }

    public function testGetActionsInstance()
    {
        $this->_actionFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue(true));

        $this->assertEquals(true, $this->_rule->getActionsInstance());
    }

    public function testGetAppliesToOptions()
    {
        $result[\Magento\TargetRule\Model\Rule::RELATED_PRODUCTS] = __('Related Products');
        $result[\Magento\TargetRule\Model\Rule::UP_SELLS] = __('Up-sells');
        $result[\Magento\TargetRule\Model\Rule::CROSS_SELLS] = __('Cross-sells');

        $this->assertEquals($result, $this->_rule->getAppliesToOptions());

        $result[''] = __('-- Please Select --');

        $this->assertEquals($result, $this->_rule->getAppliesToOptions('test'));
    }

    public function testPrepareMatchingProducts()
    {
        $productCollection = $this->_getCleanMock('\Magento\Catalog\Model\ResourceModel\Product\Collection');

        $productCollection->expects($this->once())
            ->method('getAllIds')
            ->will($this->returnValue([1, 2, 3]));

        $productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            ['getCollection', '__sleep', '__wakeup', 'load', 'getId'],
            [],
            '',
            false
        );

        $productMock->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($productCollection));

        $productMock->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());

        $this->_productFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($productMock));

        /**
         * @var \Magento\Rule\Model\Condition\Combine|\PHPUnit_Framework_MockObject_MockObject $conditions
         */
        $conditions = $this->getMock(
            '\Magento\Rule\Model\Condition\Combine',
            ['collectValidatedAttributes'],
            [],
            '',
            false
        );

        $conditions->expects($this->once())
            ->method('collectValidatedAttributes')
            ->with($productCollection);

        $this->_sqlBuilderMock->expects($this->once())
            ->method('attachConditionToCollection')
            ->with($productCollection, $conditions);

        $this->_rule->setConditions($conditions);
        $this->_rule->prepareMatchingProducts();
        $this->assertEquals([1, 2, 3], $this->_rule->getMatchingProductIds());
    }

    public function testCheckDateForStore()
    {
        $storeId = 1;
        $this->_localeDate->expects($this->once())
            ->method('isScopeDateInInterval')
            ->will($this->returnValue(true));
        $this->assertEquals(true, $this->_rule->checkDateForStore($storeId));
    }

    public function testGetPositionsLimit()
    {
        $this->assertEquals(20, $this->_rule->getPositionsLimit());

        $this->_rule->setData('positions_limit', 10);
        $this->assertEquals(10, $this->_rule->getPositionsLimit());
    }

    public function testGetActionSelectBind()
    {
        $this->assertEquals(null, $this->_rule->getActionSelectBind());

        $result = [1 => 'test'];
        $this->_rule->setData('action_select_bind', serialize($result));
        $this->assertEquals($result, $this->_rule->getActionSelectBind());

        $this->_rule->setActionSelectBind($result);
        $this->assertEquals($result, $this->_rule->getActionSelectBind());
    }

    public function testValidateData()
    {
        $object = $this->_getCleanMock('\Magento\Framework\DataObject');
        $this->assertEquals(true, $this->_rule->validateData($object));

        $object = $this->getMock('\Magento\Framework\DataObject', ['getData'], [], '', false);
        $array['actions'] = [1 => 'test'];

        $object->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($array));

        $this->assertEquals(true, $this->_rule->validateData($object));

        $object = $this->getMock('\Magento\Framework\DataObject', ['getData'], [], '', false);
        $array['actions'] = [2 => ['type' => '\Magento\Framework\DataObject', 'attribute' => 'test attribute']];

        $object->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($array));

        $result = [ 0 => __(
            'This attribute code is invalid. Please use only letters (a-z), numbers (0-9) or underscores (_),'
            . ' and be sure the code begins with a letter.'
        )];
        $this->assertEquals($result, $this->_rule->validateData($object));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Model class name for attribute is invalid
     */
    public function testValidateDataWithException()
    {
        $object = $this->getMock('\Magento\Framework\DataObject', ['getData'], [], '', false);
        $array['actions'] = [2 => ['type' => 'test type', 'attribute' => 'test attribute']];

        $object->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($array));

        $this->_rule->validateData($object);
    }

    public function testValidateByEntityId()
    {
        $combine = $this->getMock(
            '\Magento\Rule\Model\Condition\Combine',
            ['setRule', 'setId', 'setPrefix'],
            [],
            '',
            false
        );

        $combine->expects($this->any())
            ->method('setRule')
            ->will($this->returnSelf());

        $combine->expects($this->any())
            ->method('setId')
            ->will($this->returnSelf());

        $this->_ruleFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($combine));

        $this->assertEquals(true, $this->_rule->validateByEntityId(1));
    }
}
