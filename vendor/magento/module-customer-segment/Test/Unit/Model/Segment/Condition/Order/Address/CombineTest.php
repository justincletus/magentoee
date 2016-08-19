<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSegment\Test\Unit\Model\Segment\Condition\Order\Address;

class CombineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerSegment\Model\Segment\Condition\Order\Address\Combine
     */
    protected $model;

    /**
     * @var \Magento\Rule\Model\Condition\Context | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\CustomerSegment\Model\ResourceModel\Segment | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceSegment;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceOrder;

    /**
     * @var \Magento\CustomerSegment\Model\ConditionFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $conditionFactory;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder('Magento\Rule\Model\Condition\Context')
            ->disableOriginalConstructor()
            ->getMock();

        $this->resourceSegment =
            $this->getMock('Magento\CustomerSegment\Model\ResourceModel\Segment', [], [], '', false);
        $this->resourceOrder = $this->getMock('Magento\Sales\Model\ResourceModel\Order', [], [], '', false);
        $this->conditionFactory = $this->getMock('Magento\CustomerSegment\Model\ConditionFactory', [], [], '', false);
        $this->model = new \Magento\CustomerSegment\Model\Segment\Condition\Order\Address\Combine(
            $this->context,
            $this->conditionFactory,
            $this->resourceSegment,
            $this->resourceOrder
        );
    }

    public function testIsSatisfiedBy()
    {
        $table = 'sales_order';
        $tableAddress = 'sales_order_address';
        $select = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);
        $connection = $this->getMockForAbstractClass('Magento\Framework\DB\Adapter\AdapterInterface');
        $this->resourceSegment->expects($this->once())->method('createSelect')->willReturn($select);
        $this->resourceOrder->expects($this->at(0))
            ->method('getTable')
            ->with('sales_order')
            ->willReturn($table);
        $this->resourceOrder->expects($this->at(1))
            ->method('getTable')
            ->with('sales_order_address')
            ->willReturn($tableAddress);
        $select->expects($this->once())
            ->method('from')
            ->with(['order_address_order' => $table], [new \Zend_Db_Expr(1)])
            ->willReturn($select);
        $select->expects($this->once())
            ->method('where')
            ->with('order_address_order.customer_id = :customer_id')
            ->willReturn($select);
        $select->expects($this->once())
            ->method('join')
            ->with(
                ['order_address' => $tableAddress],
                'order_address.parent_id = order_address_order.entity_id',
                []
            )->willReturn($select);
        $select->expects($this->once())
            ->method('limit')
            ->with(1)
            ->willReturn($select);
        $this->resourceOrder->expects($this->atLeastOnce())->method('getConnection')->willReturn($connection);
        $connection->expects($this->once())->method('fetchOne')->willReturn(1);
        $this->assertTrue($this->model->isSatisfiedBy(1, 1, []));
    }
}
