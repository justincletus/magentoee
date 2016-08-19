<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSegment\Test\Unit\Model\ResourceModel;

class SegmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerSegment\Model\ResourceModel\Segment
     */
    protected $_resourceModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configShare;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_conditions;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_segment;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queryResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateTimeMock;

    protected function setUp()
    {
        $this->connectionMock = $this->getMockForAbstractClass(
            'Magento\Framework\DB\Adapter\AdapterInterface',
            [],
            '',
            false,
            true,
            true,
            ['query', 'insertMultiple', 'beginTransaction', 'commit']
        );

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
        )->willReturn($this->connectionMock);

        $this->_configShare = $this->getMock(
            'Magento\Customer\Model\Config\Share',
            ['isGlobalScope', '__wakeup'],
            [],
            '',
            false
        );
        $this->_segment = $this->getMock(
            'Magento\CustomerSegment\Model\Segment',
            ['getConditions', 'getWebsiteIds', 'getId', '__wakeup'],
            [],
            '',
            false
        );

        $this->_conditions = $this->getMock(
            'Magento\CustomerSegment\Model\Segment\Condition\Combine\Root',
            ['getConditions', 'getSatisfiedIds'],
            [],
            '',
            false
        );

        $contextMock = $this->getMock('\Magento\Framework\Model\ResourceModel\Db\Context', [], [], '', false);
        $contextMock->expects($this->once())->method('getResources')->willReturn($this->_resource);
        $this->queryResolverMock = $this->getMock('Magento\Quote\Model\QueryResolver', [], [], '', false);
        $this->dateTimeMock = $this->getMock('Magento\Framework\Stdlib\DateTime', [], [], '', true);
        $this->_resourceModel = new \Magento\CustomerSegment\Model\ResourceModel\Segment(
            $contextMock,
            $this->getMock('Magento\CustomerSegment\Model\ResourceModel\Helper', [], [], '', false),
            $this->_configShare,
            $this->dateTimeMock,
            $this->getMock('Magento\Quote\Model\ResourceModel\Quote', [], [], '', false),
            $this->queryResolverMock
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testSaveCustomersFromSelect()
    {
        $select =
            $this->getMock('Magento\Framework\DB\Select', ['joinLeft', 'from', 'columns'], [], '', false);
        $this->_segment->expects($this->any())->method('getId')->will($this->returnValue(3));
        $statement = $this->getMock(
            'Zend_Db_Statement',
            ['closeCursor', 'columnCount', 'errorCode', 'errorInfo', 'fetch', 'nextRowset', 'rowCount'],
            [],
            '',
            false
        );
        $websites = [8, 9];
        $statement->expects(
            $this->at(0)
        )->method(
            'fetch'
        )->will(
            $this->returnValue(['entity_id' => 4, 'website_id' => $websites[0]])
        );
        $statement->expects(
            $this->at(1)
        )->method(
            'fetch'
        )->will(
            $this->returnValue(['entity_id' => 5, 'website_id' => $websites[1]])
        );
        $statement->expects($this->at(2))->method('fetch')->will($this->returnValue(false));
        $this->connectionMock->expects(
            $this->any()
        )->method(
            'query'
        )->with(
            $this->equalTo($select)
        )->will(
            $this->returnValue($statement)
        );
        $callback = function ($data) use ($websites) {
            foreach ($data as $item) {
                if (!isset($item['website_id']) || !in_array($item['website_id'], $websites)) {
                    return false;
                }
            }
            return true;
        };

        $this->connectionMock->expects(
            $this->once()
        )->method(
            'insertMultiple'
        )->with(
            $this->equalTo('magento_customersegment_customer'),
            $this->callback($callback)
        );
        $this->connectionMock->expects($this->once())->method('beginTransaction');
        $this->connectionMock->expects($this->once())->method('commit');

        $this->_resourceModel->saveCustomersFromSelect($this->_segment, $select);
    }

    /**
     * @dataProvider aggregateMatchedCustomersDataProvider
     * @param bool $scope
     * @param array $websites
     * @param mixed $websiteIds
     */
    public function testAggregateMatchedCustomersOneWebsite($scope, $websites, $websiteIds)
    {
        $nowDate = '2015-04-23 02:04:51';
        $this->dateTimeMock->expects($this->any())
            ->method('formatDate')
            ->withAnyParameters()
            ->willReturn($nowDate);

        $customerIds = [1];
        $this->_conditions->expects(
            $this->once()
        )->method(
            'getSatisfiedIds'
        )->with(
            $this->equalTo($websiteIds)
        )->will(
            $this->returnValue($customerIds)
        );
        $this->_segment->expects($this->once())->method('getConditions')->will($this->returnValue($this->_conditions));
        $this->_segment->expects($this->once())->method('getWebsiteIds')->will($this->returnValue($websites));
        $this->_segment->expects($this->any())->method('getId')->will($this->returnValue(3));
        $insertData = [
            [
                'segment_id' => 3,
                'customer_id' => 1,
                'website_id' => $scope ? $websites : current($websites),
                'added_date' => $nowDate,
                'updated_date' => $nowDate,
            ],
        ];
        $this->connectionMock->expects(
            $this->once()
        )->method(
            'insertMultiple'
        )->with(
            $this->equalTo('magento_customersegment_customer'),
            $insertData
        );
        $this->connectionMock->expects($this->exactly(2))->method('beginTransaction');
        $this->connectionMock->expects($this->exactly(2))->method('commit');

        $this->_configShare->expects($this->any())->method('isGlobalScope')->will($this->returnValue($scope));
        $this->_resourceModel->aggregateMatchedCustomers($this->_segment);
    }

    public function aggregateMatchedCustomersDataProvider()
    {
        return [[true, [7], [7]], [false, [6], 6]];
    }
}
