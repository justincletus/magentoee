<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Model\ResourceModel;

use Magento\Catalog\Model\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tested model
     *
     * @var \Magento\TargetRule\Model\ResourceModel\Rule
     */
    protected $resourceRule;

    /**
     * Module manager mock
     *
     * @var \Magento\Framework\Module\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleManagerMock;

    /**
     * Event manager mock
     *
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * Cache context mock
     *
     * @var \Magento\Framework\Indexer\CacheContext|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheContextMock;

    /**
     * Rule Model mock
     *
     * @var \Magento\Framework\Model\AbstractModel|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleModelMock;

    /**
     * DB Adapter mock
     *
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;

    /**
     * App resource mock
     *
     * @var \Magento\Framework\App\ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appResourceMock;

    protected function setUp()
    {
        $this->moduleManagerMock = $this->getMock('Magento\Framework\Module\Manager', [], [], '', false);
        $this->eventManagerMock = $this->getMock('Magento\Framework\Event\ManagerInterface');
        $this->cacheContextMock = $this->getMock('Magento\Framework\Indexer\CacheContext', [], [], '', false);
        $this->ruleModelMock = $this->getMock('Magento\TargetRule\Model\Rule', [], [], '', false);

        $this->connectionMock = $this->getMock(
            'Magento\Framework\DB\Adapter\Pdo\Mysql',
            ['_connect', 'delete', 'insertOnDuplicate'],
            [],
            '',
            false
        );
        $this->connectionMock->expects($this->any())->method('describeTable')->will($this->returnValue([]));
        $this->connectionMock->expects($this->any())->method('lastInsertId')->will($this->returnValue(1));

        $this->appResourceMock = $this->getMock('Magento\Framework\App\ResourceConnection', [], [], '', false);
        $this->appResourceMock->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->connectionMock));

        $contextMock = $this->getMock('\Magento\Framework\Model\ResourceModel\Db\Context', [], [], '', false);
        $contextMock->expects($this->once())->method('getResources')->willReturn($this->appResourceMock);

        $this->resourceRule = (new ObjectManager($this))->getObject(
            'Magento\TargetRule\Model\ResourceModel\Rule',
            [
                'moduleManager' => $this->moduleManagerMock,
                'eventManager' => $this->eventManagerMock,
                'cacheContext' => $this->cacheContextMock,
                'context' => $contextMock
            ]
        );
    }

    public function testSaveCustomerSegments()
    {
        $ruleId = 1;
        $segmentIds = [1, 2];

        $this->connectionMock->expects($this->at(2))
            ->method('insertOnDuplicate')
            ->will($this->returnSelf());

        $this->connectionMock->expects($this->once())
            ->method('delete')
            ->with($this->resourceRule->getTable('magento_targetrule_customersegment'))
            ->will($this->returnSelf());

        $this->resourceRule->saveCustomerSegments($ruleId, $segmentIds);
    }

    public function testCleanCachedDataByProductIds()
    {
        $productIds = [1, 2, 3];
        $this->moduleManagerMock->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_PageCache')
            ->will($this->returnValue(true));

        $this->cacheContextMock->expects($this->once())
            ->method('registerEntities')
            ->with(Product::CACHE_TAG, $productIds)
            ->will($this->returnSelf());

        $this->eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with('clean_cache_by_tags', ['object' => $this->cacheContextMock])
            ->will($this->returnSelf());

        $this->resourceRule->cleanCachedDataByProductIds($productIds);
    }

    public function testBindRuleToEntity()
    {
        $this->appResourceMock->expects($this->any())
            ->method('getTableName')
            ->with('magento_targetrule_product')
            ->will($this->returnValue('magento_targetrule_product'));

        $this->connectionMock->expects($this->any())
            ->method('insertOnDuplicate')
            ->with('magento_targetrule_product', [['product_id' => 1, 'rule_id' => 1]], ['rule_id']);

        $this->connectionMock->expects($this->never())
            ->method('beginTransaction');
        $this->connectionMock->expects($this->never())
            ->method('commit');
        $this->connectionMock->expects($this->never())
            ->method('rollback');

        $this->resourceRule->bindRuleToEntity([1], [1], 'product');
    }
}
