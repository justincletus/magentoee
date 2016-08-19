<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\ForeignKey\Test\Unit\Strategy;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class SetNullTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;

    /**
     * @var \Magento\Framework\ForeignKey\Strategy\SetNull
     */
    protected $strategy;

    protected function setUp()
    {
        $this->connectionMock = $this->getMock('\Magento\Framework\DB\Adapter\AdapterInterface', [], [], '', false);
        $objectManager = new ObjectManager($this);
        $this->strategy = $objectManager->getObject('Magento\Framework\ForeignKey\Strategy\SetNull');
    }

    public function testProcess()
    {
        $constraintMock = $this->getMock('\Magento\Framework\ForeignKey\ConstraintInterface', [], [], '', false);
        $condition = 'cond1';
        $tableName = 'large_table';
        $fieldName = 'first';

        $constraintMock->expects($this->once())->method('getTableName')->willReturn($tableName);
        $constraintMock->expects($this->once())->method('getFieldName')->willReturn($fieldName);

        $this->connectionMock->expects($this->once())
            ->method('update')
            ->with($tableName, [$fieldName => null], $condition);
        $this->strategy->process($this->connectionMock, $constraintMock, $condition);
    }

    public function testLockAffectedData()
    {
        $table = 'sampleTable';
        $condition = 'sampleCondition';
        $fields = [4, 5, 6, 7];
        $affectedData = ['item1', 'item2'];

        $selectMock = $this->getMock('\Magento\Framework\DB\Select', [], [], '', false);
        $this->connectionMock->expects($this->once())->method('select')->willReturn($selectMock);

        $selectMock->expects($this->once())->method('forUpdate')->with(true)->willReturnSelf();
        $selectMock->expects($this->once())->method('from')->with($table, $fields)->willReturnSelf();
        $selectMock->expects($this->once())->method('where')->with($condition)->willReturnSelf();

        $this->connectionMock->expects($this->once())->method('fetchAssoc')->willReturn($affectedData);

        $result = $this->strategy->lockAffectedData($this->connectionMock, $table, $condition, $fields);
        $this->assertEquals($affectedData, $result);
    }
}
