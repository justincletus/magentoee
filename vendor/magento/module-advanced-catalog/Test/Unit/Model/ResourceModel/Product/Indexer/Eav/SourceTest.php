<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCatalog\Test\Unit\Model\ResourceModel\Product\Indexer\Eav;

class SourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdvancedCatalog\Model\ResourceModel\Product\Indexer\Eav\Source
     */
    protected $_source;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $selectMock;

    /**
     * @var \Magento\Framework\DB\Adapter\Pdo\Mysql|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;

    /**
     * @var \Magento\Framework\App\ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eavConfigMock;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Helper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeMock;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendAttributeMock;

    /**
     * @var \Magento\Framework\Indexer\Table\StrategyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $tableStrategyMock;

    public function setUp()
    {
        $this->selectMock = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);
        $this->selectMock->expects($this->any())->method('from')->will($this->returnValue($this->selectMock));
        $this->selectMock->expects($this->any())->method('join')->will($this->returnValue($this->selectMock));
        $this->selectMock->expects($this->any())->method('group')->will($this->returnValue($this->selectMock));
        $this->selectMock->expects($this->any())->method('where')->will($this->returnValue($this->selectMock));

        $this->connectionMock = $this->getMock('Magento\Framework\DB\Adapter\Pdo\Mysql', [], [], '', false);
        $this->connectionMock->expects($this->any())->method('select')->willReturn($this->selectMock);
        $this->connectionMock->expects($this->any())->method('describeTable')->willReturn(['column1', 'column2']);

        $this->resourceMock = $this->getMock('Magento\Framework\App\ResourceConnection', [], [], '', false);
        $this->resourceMock->expects($this->any())->method('getConnection')->willReturn($this->connectionMock);

        $this->contextMock = $this->getMock('Magento\Framework\Model\ResourceModel\Db\Context', [], [], '', false);
        $this->contextMock->expects($this->once())->method('getResources')->willReturn($this->resourceMock);

        $this->attributeMock = $this->getMock(
            'Magento\Eav\Model\Entity\Attribute\AbstractAttribute',
            [],
            [],
            '',
            false
        );
        $this->backendAttributeMock = $this->getMock(
            'Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend',
            [],
            [],
            '',
            false
        );
        $this->attributeMock->expects($this->any())->method('getBackend')
            ->will($this->returnValue($this->backendAttributeMock));

        $this->eavConfigMock = $this->getMock('Magento\Eav\Model\Config', [], [], '', false);
        $this->eavConfigMock->expects($this->any())->method('getAttribute')->will(
            $this->returnValue($this->attributeMock)
        );

        $this->eventManagerMock = $this->getMockForAbstractClass(
            'Magento\Framework\Event\ManagerInterface',
            [],
            '',
            false,
            true,
            true,
            ['dispatch']
        );

        $this->helperMock = $this->getMock('Magento\Catalog\Model\ResourceModel\Helper', [], [], '', false);

        $connectionName = 'index';

        $this->tableStrategyMock = $this->getMock(
            'Magento\Framework\Indexer\Table\StrategyInterface',
            [],
            [],
            '',
            false
        );
        $this->tableStrategyMock->expects($this->any())->method('getTableName')->will($this->returnArgument(0));

        $this->_source = new \Magento\AdvancedCatalog\Model\ResourceModel\Product\Indexer\Eav\Source(
            $this->contextMock,
            $this->tableStrategyMock,
            $this->eavConfigMock,
            $this->eventManagerMock,
            $this->helperMock,
            $connectionName
        );
    }

    /**
     * Test prepare relation index with using idx table
     *
     * @return void
     */
    public function testPrepareRelationIndexUseIdxTable()
    {
        $this->tableStrategyMock->expects($this->any())->method('getUseIdxTable')->willReturn(true);
        $this->eventManagerMock->expects($this->once())->method('dispatch')->with(
            'prepare_catalog_product_index_select'
        );
        $this->connectionMock->expects($this->never())->method('createTemporaryTableLike');
        $this->connectionMock->expects($this->never())->method('dropTemporaryTable');
        $this->assertInstanceOf(
            '\Magento\AdvancedCatalog\Model\ResourceModel\Product\Indexer\Eav\Source',
            $this->_source->reindexAll()
        );
    }

    /**
     * Test prepare relation index without using idx table
     *
     * @return void
     */
    public function testPrepareRelationIndexNotUseIdxTable()
    {
        $this->tableStrategyMock->expects($this->any())->method('getUseIdxTable')->willReturn(false);
        $this->eventManagerMock->expects($this->once())->method('dispatch')->with(
            'prepare_catalog_product_index_select'
        );
        $this->connectionMock->expects($this->atLeastOnce())->method('createTemporaryTableLike');
        $this->connectionMock->expects($this->once())->method('dropTemporaryTable');
        $this->assertInstanceOf(
            '\Magento\AdvancedCatalog\Model\ResourceModel\Product\Indexer\Eav\Source',
            $this->_source->reindexEntities([1])
        );
    }
}
