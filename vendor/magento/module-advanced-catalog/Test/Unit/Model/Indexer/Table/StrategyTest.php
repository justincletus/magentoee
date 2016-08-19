<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCatalog\Test\Unit\Model\Indexer\Table;

class StrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Strategy object
     *
     * @var \Magento\AdvancedCatalog\Model\Indexer\Table\Strategy
     */
    protected $_model;

    /**
     * Resource mock
     *
     * @var \Magento\Framework\App\ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceMock;

    /**
     * Adapter mock
     *
     * @var \Magento\Framework\DB\Adapter\Pdo\Mysql|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapterMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->_resourceMock = $this->getMock(
            '\Magento\Framework\App\ResourceConnection',
            [],
            [],
            '',
            false
        );
        $this->_adapterMock = $this->getMock('Magento\Framework\DB\Adapter\Pdo\Mysql', [], [], '', false);
        $this->_resourceMock->expects($this->any())->method('getConnection')->willReturn($this->_adapterMock);

        $this->_model = new \Magento\AdvancedCatalog\Model\Indexer\Table\Strategy(
            $this->_resourceMock
        );
    }

    /**
     * Test use idx table switcher
     *
     * @return void
     */
    public function testUseIdxTable()
    {
        $this->assertEquals(false, $this->_model->getUseIdxTable());
        $this->_model->setUseIdxTable(false);
        $this->assertEquals(false, $this->_model->getUseIdxTable());
        $this->_model->setUseIdxTable(true);
        $this->assertEquals(true, $this->_model->getUseIdxTable());
        $this->_model->setUseIdxTable();
        $this->assertEquals(false, $this->_model->getUseIdxTable());
    }

    /**
     * Test prepare table name with using idx table
     *
     * @return void
     */
    public function testPrepareTableNameUseIdxTable()
    {
        $this->_adapterMock->expects($this->never())->method('createTemporaryTableLike')->with(
            'test_temp',
            'test_tmp',
            true
        );
        $this->_model->setUseIdxTable(true);
        $this->assertEquals('test_idx', $this->_model->prepareTableName('test'));
    }

    /**
     * Test prepare table name without using idx table
     *
     * @return void
     */
    public function testPrepareTableNameNotUseIdxTable()
    {
        $prefix = 'pre_';
        $this->_resourceMock->expects($this->any())->method('getTableName')->will(
            $this->returnCallback(
                function ($tableName) use ($prefix) {
                    return $prefix . $tableName;
                }
            )
        );
        $this->_adapterMock->expects($this->once())->method('createTemporaryTableLike')->with(
            'pre_test_temp',
            'pre_test_tmp',
            true
        );
        $this->_model->setUseIdxTable(false);
        $this->assertEquals('test_temp', $this->_model->prepareTableName('test'));
    }

    /**
     * Test table name getter
     *
     * @return void
     */
    public function testGetTableName()
    {
        $prefix = 'pre_';
        $this->_resourceMock->expects($this->any())->method('getTableName')->will(
            $this->returnCallback(
                function ($tableName) use ($prefix) {
                    return $prefix . $tableName;
                }
            )
        );
        $this->assertEquals('pre_test_temp', $this->_model->getTableName('test'));
        $this->_model->setUseIdxTable(true);
        $this->assertEquals('pre_test_idx', $this->_model->getTableName('test'));
    }
}
