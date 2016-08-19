<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\ForeignKey\Test\Unit\Strategy;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class DbCascadeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;

    /**
     * @var \Magento\Framework\ForeignKey\Strategy\Cascade
     */
    protected $strategy;

    protected function setUp()
    {
        $this->connectionMock = $this->getMock('\Magento\Framework\DB\Adapter\AdapterInterface');
        $objectManager = new ObjectManager($this);
        $this->strategy = $objectManager->getObject('Magento\Framework\ForeignKey\Strategy\DbCascade');
    }

    public function testProcess()
    {
        $constraintMock = $this->getMock('\Magento\Framework\ForeignKey\ConstraintInterface');
        $this->connectionMock->expects($this->never())->method('delete');
        $this->strategy->process($this->connectionMock, $constraintMock, 'cond1');
    }

    public function testLockAffectedData()
    {
        $table = 'sampleTable';
        $condition = 'sampleCondition';
        $fields = [3, 75, 56, 67];
        $affectedData = ['item1', 'item2'];

        $selectMock = $this->getMock('\Magento\Framework\DB\Select', [], [], '', false);
        $this->connectionMock->expects($this->once())->method('select')->willReturn($selectMock);

        $selectMock->expects($this->once())->method('forUpdate')->with(true);
        $selectMock->expects($this->once())->method('from')->with($table, $fields);
        $selectMock->expects($this->once())->method('where')->with($condition);
        $this->connectionMock->expects($this->once())->method('fetchAssoc')->willReturn($affectedData);
        $result = $this->strategy->lockAffectedData($this->connectionMock, $table, $condition, $fields);
        $this->assertEquals($affectedData, $result);
    }
}
