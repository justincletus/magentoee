<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Test\Unit\Model\ResourceModel;

class BannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Banner\Model\ResourceModel\Banner
     */
    private $_resourceModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_eventManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_bannerConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $connection;

    protected function setUp()
    {
        $this->connection = $this->getMockForAbstractClass(
            'Magento\Framework\DB\Adapter\Pdo\Mysql',
            [],
            '',
            false,
            true,
            true,
            ['getTransactionLevel', 'fetchOne', 'select', 'prepareSqlCondition', '_connect', '_quote']
        );
        $select = new \Magento\Framework\DB\Select($this->connection);

        $this->connection->expects($this->once())->method('select')->will($this->returnValue($select));
        $this->connection->expects($this->any())->method('_quote')->will($this->returnArgument(0));

        $this->_resource = $this->getMock(
            'Magento\Framework\App\ResourceConnection',
            [],
            [],
            '',
            false
        );
        $this->_resource->expects($this->any())->method('getTableName')->will($this->returnArgument(0));
        $this->_resource->expects(
            $this->any()
        )->method(
            'getConnection'
        )->will(
            $this->returnValue($this->connection)
        );

        $this->_eventManager = $this->getMock(
            'Magento\Framework\Event\ManagerInterface',
            ['dispatch'],
            [],
            '',
            false
        );

        $this->_bannerConfig = $this->getMock(
            'Magento\Banner\Model\Config',
            ['explodeTypes'],
            [],
            '',
            false
        );

        $salesruleColFactory = $this->getMock(
            'Magento\Banner\Model\ResourceModel\Salesrule\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $catRuleColFactory = $this->getMock(
            'Magento\Banner\Model\ResourceModel\Catalogrule\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $contextMock = $this->getMock('\Magento\Framework\Model\ResourceModel\Db\Context', [], [], '', false);
        $contextMock->expects($this->once())->method('getResources')->willReturn($this->_resource);

        $this->_resourceModel = new \Magento\Banner\Model\ResourceModel\Banner(
            $contextMock,
            $this->_eventManager,
            $this->_bannerConfig,
            $salesruleColFactory,
            $catRuleColFactory
        );
    }

    protected function tearDown()
    {
        $this->_resourceModel = null;
        $this->_resource = null;
        $this->_eventManager = null;
        $this->_bannerConfig = null;
        $this->connection = null;
    }

    public function testGetStoreContent()
    {
        $this->connection->expects(
            $this->once()
        )->method(
            'fetchOne'
        )->with(
            $this->logicalAnd(
                $this->isInstanceOf('Magento\Framework\DB\Select'),
                $this->equalTo(
                    'SELECT `main_table`.`banner_content`' .
                    ' FROM `magento_banner_content` AS `main_table`' .
                    ' WHERE (main_table.banner_id = 123) AND (main_table.store_id IN (5, 0))' .
                    ' ORDER BY `main_table`.`store_id` DESC'
                )
            )
        )->will(
            $this->returnValue('Banner Contents')
        );

        $this->_eventManager->expects(
            $this->once()
        )->method(
            'dispatch'
        )->with(
            'magento_banner_resource_banner_content_select_init',
            $this->arrayHasKey('select')
        );

        $this->assertEquals('Banner Contents', $this->_resourceModel->getStoreContent(123, 5));
    }

    public function testGetStoreContentFilterByTypes()
    {
        $bannerTypes = ['content', 'footer', 'header'];
        $this->_bannerConfig->expects(
            $this->once()
        )->method(
            'explodeTypes'
        )->with(
            $bannerTypes
        )->will(
            $this->returnValue(['footer', 'header'])
        );
        $this->_resourceModel->filterByTypes($bannerTypes);

        $this->connection->expects(
            $this->exactly(2)
        )->method(
            'prepareSqlCondition'
        )->will(
            $this->returnValueMap(
                [
                    ['banner.types', ['finset' => 'footer'], 'banner.types IN ("footer")'],
                    ['banner.types', ['finset' => 'header'], 'banner.types IN ("header")'],
                ]
            )
        );
        $this->connection->expects(
            $this->once()
        )->method(
            'fetchOne'
        )->with(
            $this->logicalAnd(
                $this->isInstanceOf('Magento\Framework\DB\Select'),
                $this->equalTo(
                    'SELECT `main_table`.`banner_content`, `banner`.*' .
                    ' FROM `magento_banner_content` AS `main_table`' .
                    "\n" .
                    ' INNER JOIN `magento_banner` AS `banner`' .
                    ' ON main_table.banner_id = banner.banner_id' .
                    ' WHERE' .
                    ' (main_table.banner_id = 123)' .
                    ' AND (main_table.store_id IN (5, 0))' .
                    ' AND (banner.types IN ("footer") OR banner.types IN ("header"))' .
                    ' ORDER BY `main_table`.`store_id` DESC'
                )
            )
        )->will(
            $this->returnValue('Banner Contents')
        );

        $this->_eventManager->expects(
            $this->once()
        )->method(
            'dispatch'
        )->with(
            'magento_banner_resource_banner_content_select_init',
            $this->arrayHasKey('select')
        );

        $this->assertEquals('Banner Contents', $this->_resourceModel->getStoreContent(123, 5));
    }
}
