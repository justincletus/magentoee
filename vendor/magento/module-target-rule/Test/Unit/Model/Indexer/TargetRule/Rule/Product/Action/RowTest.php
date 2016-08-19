<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  unit_tests
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Model\Indexer\TargetRule\Rule\Product\Action;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class RowTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tested model
     *
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action\Row
     */
    protected $model;

    /**
     * Product factory mock
     *
     * @var \Magento\Catalog\Model\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactoryMock;

    /**
     * Rule Factory mock
     *
     * @var \Magento\TargetRule\Model\RuleFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleFactoryMock;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->ruleFactoryMock = $this->getMock('Magento\TargetRule\Model\RuleFactory', ['create'], [], '', false);
        $this->model = $objectManager->getObject(
            'Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action\Row',
            [
                'productFactory' => $this->getMock('Magento\Catalog\Model\ProductFactory', ['create'], [], '', false),
                'ruleFactory' => $this->ruleFactoryMock,
                'ruleCollectionFactory' => $this->getMock(
                    'Magento\TargetRule\Model\ResourceModel\Rule\CollectionFactory',
                    ['create'],
                    [],
                    '',
                    false
                ),
                'resource' => $this->getMock('\Magento\TargetRule\Model\ResourceModel\Index', [], [], '', false),
                'storeManager' => $this->getMockForAbstractClass(
                    '\Magento\Store\Model\StoreManagerInterface',
                    [],
                    '',
                    false
                ),
                'localeDate' => $this->getMockForAbstractClass(
                    '\Magento\Framework\Stdlib\DateTime\TimezoneInterface',
                    [],
                    '',
                    false
                ),
            ]
        );
    }

    /**
     * Test for exec with empty IDs
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage We can't rebuild the index for an undefined product.
     */
    public function testEmptyId()
    {
        $this->model->execute(null);
    }

    public function testCleanProductPagesCache()
    {
        $ruleId = 1;
        $oldProductIds = [1, 2];
        $newProductIds = [2, 3];
        $productsToClean = array_unique(array_merge($oldProductIds, $newProductIds));
        $rule = $this->getMock(
            '\Magento\TargetRule\Model\Rule',
            ['load', 'getResource', 'getMatchingProductIds', 'getId', '__sleep', '__wakeup'],
            [],
            '',
            false
        );
        $rule->expects($this->once())->method('load')->with($ruleId);
        $ruleResource = $this->getMock(
            '\Magento\TargetRule\Model\ResourceModel\Rule',
            [
                '__sleep',
                '__wakeup',
                'getAssociatedEntityIds',
                'unbindRuleFromEntity',
                'bindRuleToEntity',
                'cleanCachedDataByProductIds'
            ],
            [],
            '',
            false
        );
        $ruleResource->expects($this->once())
            ->method('getAssociatedEntityIds')
            ->with($ruleId, 'product')
            ->will($this->returnValue($oldProductIds));

        $ruleResource->expects($this->once())
            ->method('unbindRuleFromEntity')
            ->with($ruleId, [], 'product');

        $ruleResource->expects($this->once())
            ->method('bindRuleToEntity')
            ->with($ruleId, $newProductIds, 'product');

        $ruleResource->expects($this->once())
            ->method('cleanCachedDataByProductIds')
            ->with($productsToClean);

        $rule->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $rule->expects($this->once())
            ->method('getMatchingProductIds')
            ->will($this->returnValue($newProductIds));

        $rule->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($ruleResource));

        $this->ruleFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($rule));

        $this->model->execute($ruleId);
    }
}
