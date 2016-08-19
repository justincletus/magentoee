<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerSegment\Test\Unit\Model\Segment\Condition\Sales;

use Magento\CustomerSegment\Model\Segment\Condition\Sales\Salesamount;
use Magento\Sales\Model\ResourceModel\Order;
use Magento\CustomerSegment\Model\ConditionFactory;
use Magento\CustomerSegment\Model\ResourceModel\Segment;
use Magento\Framework\View\Layout;

/**
 * Class SalesamountTest
 */
class SalesamountTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Salesamount
     */
    protected $model;

    /**
     * @var Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderResourceMock;

    /**
     * @var ConditionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $conditionFactoryMock;

    /**
     * @var \Magento\CustomerSegment\Model\ResourceModel\Segment | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceSegment;

    /**
     * @var Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    /**
     * Test setUp
     */
    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->orderResourceMock = $this->getMock(
            'Magento\Sales\Model\ResourceModel\Order',
            [],
            [],
            '',
            false
        );
        $this->layout = $this->getMock('Magento\Framework\View\Layout', [], [], '', false);
        $ruleContextMock = $this->getMockBuilder('Magento\Rule\Model\Condition\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $ruleContextMock->method('getLayout')->willReturn($this->layout);
        $this->resourceSegment =
            $this->getMock('Magento\CustomerSegment\Model\ResourceModel\Segment', [], [], '', false);

        $this->conditionFactoryMock = $this->getMock(
            'Magento\CustomerSegment\Model\ConditionFactory',
            [],
            [],
            '',
            false
        );

        $this->model = $objectManager->getObject(
            'Magento\CustomerSegment\Model\Segment\Condition\Sales\Salesamount',
            [
                'context' => $ruleContextMock,
                'orderResource' => $this->orderResourceMock,
                'conditionFactory' => $this->conditionFactoryMock,
                'resourceSegment' => $this->resourceSegment
            ]
        );
    }



    /**
     * @dataProvider conditionProvider
     */
    public function testGetConditionsSql($operator, $value, $attribute, $checkSql)
    {
        $website = 1;
        $salesOrderTable = 'sales_order_table';
        $storeTable = 'store_table';
        $checkSqlResult = $checkSql . ' ' . $operator . ' ' . (double)$value;
        $storeIds = [1, 2];

        $this->model->setData('operator', $operator);
        $this->model->setData('value', $value);
        $this->model->setData('attribute', $attribute);

        $select = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);
        $select->expects($this->once())
            ->method('from')
            ->with(['sales_order' => $salesOrderTable], ['sales_order.customer_id'])
            ->willReturnSelf();
        $select->expects($this->once())
            ->method('group')
            ->with(['sales_order.customer_id'])
            ->willReturnSelf();
        $select->expects($this->once())
            ->method('having')
            ->with(new \Zend_Db_Expr($checkSqlResult))
            ->willReturnSelf();
        $select->expects($this->exactly(2))
            ->method('where')
            ->withConsecutive(
                ['sales_order.customer_id IS NOT NULL'],
                ['sales_order.store_id IN (?)', implode(',', $storeIds)]
            )
            ->willReturnSelf();

        $storeSelect = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->disableOriginalConstructor()
            ->getMock();

        $storeSelect->expects($this->once())
            ->method('from')
            ->with(['store' => $storeTable], ['store.store_id'])
            ->willReturnSelf();
        $storeSelect->expects($this->once())
            ->method('where')
            ->with('store.website_id IN (?)', $website)
            ->willReturnSelf();

        $this->resourceSegment->expects($this->exactly(2))
            ->method('createSelect')
            ->willReturnOnConsecutiveCalls($select, $storeSelect);

        $this->resourceSegment->expects($this->once())
            ->method('getSqlOperator')
            ->willReturn($operator);

        $connection = $this->getMockForAbstractClass('Magento\Framework\DB\Adapter\AdapterInterface');
        $this->resourceSegment->expects($this->any())
            ->method('getConnection')
            ->willReturn($connection);
        $connection->expects($this->once())
            ->method('fetchCol')
            ->with($storeSelect)
            ->willReturn($storeIds);
        $connection->expects($this->once())
            ->method('quote')
            ->with((double) $value)
            ->willReturn((double) $value);
        //for getConditionSql()
        $connection->expects($this->exactly(2))
            ->method('getCheckSql')
            ->withConsecutive(
                [$checkSql . ' IS NOT NULL', $checkSql, 0],
                [$checkSqlResult, 1, 0]
            )->willReturnOnConsecutiveCalls($checkSql, $checkSqlResult);

        $this->resourceSegment->expects($this->exactly(2))
            ->method('getTable')
            ->willReturnMap([['sales_order', $salesOrderTable], ['store', $storeTable]]);

        $this->assertEquals($select, $this->model->getConditionsSql(null, 1, false));
    }

    public function conditionProvider()
    {
        return [
            ['>', null, 'total', 'SUM(sales_order.base_grand_total)'],
            ['=', 0, 'average', 'AVG(sales_order.base_grand_total)'],
            ['<', 1, 'total', 'SUM(sales_order.base_grand_total)']
        ];
    }
}
