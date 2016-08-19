<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\CatalogEvent\Model\ResourceModel\Event\Collection
 */
namespace Magento\CatalogEvent\Test\Unit\Model\ResourceModel\Event;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Main table name
     */
    const MAIN_TABLE = 'main_table';

    /**#@+
     * Predefined store ids
     */
    const STORE_ID = 0;

    const CURRENT_STORE_ID = 1;

    /**#@-*/

    /**
     * Predefined getCheckSql result
     */
    const GET_CHECK_SQL_RESULT = 'sql_result';

    /**
     * Expected values for leftJoin method
     *
     * @var array
     */
    protected $_joinValues = [
        2 => [
            'name' => ['event_image' => self::MAIN_TABLE],
            'condition' => 'event_image.event_id = main_table.event_id AND event_image.store_id = %CURRENT_STORE_ID%',
            'columns' => ['image' => self::GET_CHECK_SQL_RESULT],
        ],
        3 => [
            'name' => ['event_image_default' => self::MAIN_TABLE],
            'condition' =>
                'event_image_default.event_id = main_table.event_id AND event_image_default.store_id = %STORE_ID%',
            'columns' => [],
        ],
    ];

    /**
     * Replace values for store ids
     *
     * @var array
     */
    protected $_joinReplaces = ['%CURRENT_STORE_ID%' => self::CURRENT_STORE_ID, '%STORE_ID%' => self::STORE_ID];

    /**
     * Expected values for getCheckSql method
     *
     * @var array
     */
    protected $_checkSqlValues = [
        'condition' => 'event_image.image IS NULL',
        'true' => 'event_image_default.image',
        'false' => 'event_image.image',
    ];

    /**
     * @var \Magento\CatalogEvent\Model\ResourceModel\Event\Collection
     */
    protected $_collection;

    protected function setUp()
    {
        foreach (array_keys($this->_joinValues) as $key) {
            $this->_joinValues[$key]['condition'] = str_replace(
                array_keys($this->_joinReplaces),
                array_values($this->_joinReplaces),
                $this->_joinValues[$key]['condition']
            );
        }

        $eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface', [], [], '', false);
        $store = $this->getMock(
            'Magento\Store\Model\Store',
            ['getId', '__sleep', '__wakeup'],
            [],
            '',
            false
        );
        $store->expects($this->once())->method('getId')->will($this->returnValue(self::CURRENT_STORE_ID));

        $storeManager = $this->getMock('Magento\Store\Model\StoreManager', ['getStore'], [], '', false);
        $storeManager->expects($this->once())->method('getStore')->will($this->returnValue($store));

        $select =
            $this->getMock('Magento\Framework\DB\Select', ['joinLeft', 'from', 'columns'], [], '', false);
        foreach ($this->_joinValues as $key => $arguments) {
            $select->expects($this->at($key))
                ->method('joinLeft')
                ->with($arguments['name'], $arguments['condition'], $arguments['columns'])
                ->will($this->returnSelf());
        }

        $connection = $this->getMock(
            'Magento\Framework\DB\Adapter\Pdo\Mysql',
            ['select', 'quoteInto', 'getCheckSql', 'quote'],
            [],
            '',
            false
        );
        $connection->expects($this->once())->method('select')->will($this->returnValue($select));
        $connection->expects($this->exactly(5))->method('quoteInto')->will(
            $this->returnCallback(
                function ($text, $value) {
                    return str_replace('?', $value, $text);
                }
            )
        );
        $connection->expects($this->exactly(1))
            ->method('getCheckSql')
            ->will($this->returnCallback([$this, 'verifyGetCheckSql']));

        $resource = $this->getMockForAbstractClass(
            'Magento\Framework\Model\ResourceModel\Db\AbstractDb',
            [],
            '',
            false,
            true,
            true,
            ['getConnection', 'getMainTable', 'getTable', '__wakeup']
        );
        $resource->expects($this->once())->method('getConnection')->will($this->returnValue($connection));
        $resource->expects($this->once())->method('getMainTable')->will($this->returnValue(self::MAIN_TABLE));
        $resource->expects($this->exactly(3))->method('getTable')->will($this->returnValue(self::MAIN_TABLE));

        $fetchStrategy = $this->getMockForAbstractClass('Magento\Framework\Data\Collection\Db\FetchStrategyInterface');
        $entityFactory = $this->getMock('Magento\Framework\Data\Collection\EntityFactory', [], [], '', false);
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $localeDate = $this->getMock('\Magento\Framework\Stdlib\DateTime\Timezone', [], [], '', false, false);
        $eavConfig = $this->getMock('\Magento\Eav\Model\Config', [], [], '', false);

        $this->_collection = new \Magento\CatalogEvent\Model\ResourceModel\Event\Collection(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storeManager,
            $localeDate,
            $eavConfig,
            null,
            $resource
        );
    }

    protected function tearDown()
    {
        $this->_collection = null;
    }

    /**
     * Callback and verify getCheckSql method arguments
     *
     * @param string $condition     expression
     * @param string $true          true value
     * @param string $false         false value
     * @return string
     */
    public function verifyGetCheckSql($condition, $true, $false)
    {
        $this->assertEquals($this->_checkSqlValues['condition'], $condition);
        $this->assertEquals($this->_checkSqlValues['true'], $true);
        $this->assertEquals($this->_checkSqlValues['false'], $false);

        return self::GET_CHECK_SQL_RESULT;
    }

    public function testAddImageData()
    {
        $this->assertInstanceOf(
            'Magento\CatalogEvent\Model\ResourceModel\Event\Collection',
            $this->_collection->addImageData()
        );
    }
}
