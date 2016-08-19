<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSegment\Test\Unit\Model;

class CustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerSegment\Model\Customer
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_customerSession;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactoryMock;

    /**
     * @var \Magento\Customer\Model\Visitor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $visitorMock;

    /**
     * @var \Magento\Framework\App\Http\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpContextMock;

    /**
     * @var array
     */
    private $_fixtureSegmentIds = [123, 456];

    protected function setUp()
    {
        $this->_registry = $this->getMock('Magento\Framework\Registry', ['registry'], [], '', false);

        $website = new \Magento\Framework\DataObject(['id' => 5]);
        $storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $storeManager->expects($this->any())->method('getWebsite')->will($this->returnValue($website));

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $constructArguments = $objectManager->getConstructArguments(
            'Magento\Customer\Model\Session',
            ['storage' => new \Magento\Framework\Session\Storage()]
        );
        $this->_customerSession = $this->getMock(
            'Magento\Customer\Model\Session',
            ['getCustomer'],
            $constructArguments
        );

        $contextMock = $this->getMock('\Magento\Framework\Model\ResourceModel\Db\Context', [], [], '', false);
        $contextMock->expects($this->once())
            ->method('getResources')
            ->willReturn($this->getMock('Magento\Framework\App\ResourceConnection', [], [], '', false));
        $this->_resource = $this->getMock(
            'Magento\CustomerSegment\Model\ResourceModel\Customer',
            ['getCustomerWebsiteSegments', 'getIdFieldName'],
            [
                $contextMock,
                $this->getMock('Magento\Framework\Stdlib\DateTime', null, [], '', true)
            ]
        );
        $this->collectionFactoryMock = $this->getMock(
            'Magento\CustomerSegment\Model\ResourceModel\Segment\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->visitorMock = $this->getMock('Magento\Customer\Model\Visitor', [], [], '', false);

        $this->httpContextMock = $this->getMock(
            'Magento\Framework\App\Http\Context',
            [],
            [],
            '',
            false
        );

        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->model = $helper->getObject(
            '\Magento\CustomerSegment\Model\Customer',
            [
                'registry' => $this->_registry,
                'resource' => $this->_resource,
                'resourceCustomer' => $this->getMock(
                    'Magento\Customer\Model\ResourceModel\Customer',
                    [],
                    [],
                    '',
                    false
                ),
                'visitor' => $this->visitorMock,
                'storeManager' => $storeManager,
                'customerSession' => $this->_customerSession,
                'httpContext' => $this->httpContextMock,
                'collectionFactory' => $this->collectionFactoryMock
            ]
        );
    }

    protected function tearDown()
    {
        $this->model = null;
        $this->_registry = null;
        $this->_customerSession = null;
        $this->_resource = null;
    }

    public function testGetCurrentCustomerSegmentIdsCustomerInRegistry()
    {
        $customer = new \Magento\Framework\DataObject(['id' => 100500]);
        $this->_registry->expects(
            $this->once()
        )->method(
            'registry'
        )->with(
            'segment_customer'
        )->will(
            $this->returnValue($customer)
        );
        $this->_resource->expects(
            $this->once()
        )->method(
            'getCustomerWebsiteSegments'
        )->with(
            100500,
            5
        )->will(
            $this->returnValue($this->_fixtureSegmentIds)
        );
        $this->assertEquals($this->_fixtureSegmentIds, $this->model->getCurrentCustomerSegmentIds());
    }

    public function testGetCurrentCustomerSegmentIdsCustomerInRegistryNoId()
    {
        $customer = new \Magento\Framework\DataObject();
        $this->_registry->expects(
            $this->once()
        )->method(
            'registry'
        )->with(
            'segment_customer'
        )->will(
            $this->returnValue($customer)
        );
        $this->_customerSession->setData('customer_segment_ids', [5 => $this->_fixtureSegmentIds]);
        $this->assertEquals($this->_fixtureSegmentIds, $this->model->getCurrentCustomerSegmentIds());
    }

    public function testGetCurrentCustomerSegmentIdsCustomerInSession()
    {
        $customer = new \Magento\Framework\DataObject(['id' => 100500]);
        $this->_customerSession->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));
        $this->_resource->expects(
            $this->once()
        )->method(
            'getCustomerWebsiteSegments'
        )->with(
            100500,
            5
        )->will(
            $this->returnValue($this->_fixtureSegmentIds)
        );
        $this->assertEquals($this->_fixtureSegmentIds, $this->model->getCurrentCustomerSegmentIds());
    }

    public function testGetCurrentCustomerSegmentIdsCustomerInSessionNoId()
    {
        $customer = new \Magento\Framework\DataObject();
        $this->_customerSession->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));
        $this->_customerSession->setData('customer_segment_ids', [5 => $this->_fixtureSegmentIds]);
        $this->assertEquals($this->_fixtureSegmentIds, $this->model->getCurrentCustomerSegmentIds());
    }

    public function testProcessEventForVisitor()
    {
        $event = 'test_event';
        $customerSegment = $this->getMock('Magento\CustomerSegment\Model\Segment', ['validateCustomer'], [], '', false);
        $customerSegment->expects($this->once())->method('validateCustomer')->willReturn(true);
        $customerSegment->setData('apply_to', \Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS);
        $customerSegment->setData('id', 'segment_id');

        $segmentCollection = $this->getMock(
            'Magento\CustomerSegment\Model\ResourceModel\Segment\Collection',
            [],
            [],
            '',
            false
        );
        $segmentCollection->expects($this->once())->method('addEventFilter')->with($event)->willReturnSelf();
        $segmentCollection->expects($this->once())->method('addWebsiteFilter')->with(5)->willReturnSelf();
        $segmentCollection->expects($this->once())->method('addIsActiveFilter')->with(1)->willReturnSelf();
        $segmentCollection->expects($this->once())->method('getIterator')->willReturn(
            new \ArrayIterator([$customerSegment])
        );
        $this->collectionFactoryMock->expects($this->once())->method('create')->willReturn($segmentCollection);

        $this->visitorMock->setData('id', 'visitor_1');
        $this->visitorMock->setData('quote_id', 'quote_1');

        $this->assertEquals($this->model, $this->model->processEvent($event, null, 1));
    }

    /**
     * @param mixed $visitorSegmentIds
     * @param int $websiteId
     * @param array $segmentIds
     * @param array $resultSegmentIds
     * @param array $contextSegmentIds
     *
     * @dataProvider dataProviderAddVisitorToWebsiteSegments
     */
    public function testAddVisitorToWebsiteSegments(
        $visitorSegmentIds,
        $websiteId,
        array $segmentIds,
        array $resultSegmentIds,
        array $contextSegmentIds
    ) {
        /**
         * @var \Magento\Framework\Session\SessionManagerInterface|\PHPUnit_Framework_MockObject_MockObject $sessionMock
         */
        $sessionMock = $this->getMockBuilder('Magento\Framework\Session\SessionManagerInterface')
            ->setMethods(['getCustomerSegmentIds', 'setCustomerSegmentIds'])
            ->getMockForAbstractClass();
        $sessionMock->expects($this->once())
            ->method('getCustomerSegmentIds')
            ->willReturn($visitorSegmentIds);
        $sessionMock->expects($this->once())
            ->method('setCustomerSegmentIds')
            ->with($resultSegmentIds);

        $this->httpContextMock->expects($this->once())
            ->method('setValue')
            ->with(\Magento\CustomerSegment\Helper\Data::CONTEXT_SEGMENT, $contextSegmentIds, $contextSegmentIds)
            ->willReturnSelf();

        $this->assertEquals(
            $this->model,
            $this->model->addVisitorToWebsiteSegments($sessionMock, $websiteId, $segmentIds)
        );
    }

    public function dataProviderAddVisitorToWebsiteSegments()
    {
        return [
            ['', 1, [], [1 => []], []],
            [[1 => [2, 3], 2 => [4]], 1, [2, 5], [1 => [2, 3, 3 => 5], 2 => [4]], [2, 3, 3 => 5]],
            [[1 => [2, 3], 3 => [4]], 2, [2, 5], [1 => [2, 3], 2 => [2, 5], 3 => [4]], [2, 5]],
            [[2 => [2, 3]], 2, [], [2 => [2, 3]], [2, 3]],
        ];
    }

    /**
     * @param mixed $visitorSegmentIds
     * @param int $websiteId
     * @param array $segmentIds
     * @param array $resultSegmentIds
     * @param array $contextSegmentIds
     *
     * @dataProvider dataProviderRemoveVisitorFromWebsiteSegments
     */
    public function testRemoveVisitorFromWebsiteSegments(
        $visitorSegmentIds,
        $websiteId,
        array $segmentIds,
        array $resultSegmentIds,
        array $contextSegmentIds
    ) {
        /**
         * @var \Magento\Framework\Session\SessionManagerInterface|\PHPUnit_Framework_MockObject_MockObject $sessionMock
         */
        $sessionMock = $this->getMockBuilder('Magento\Framework\Session\SessionManagerInterface')
            ->setMethods(['getCustomerSegmentIds', 'setCustomerSegmentIds'])
            ->getMockForAbstractClass();
        $sessionMock->expects($this->once())
            ->method('getCustomerSegmentIds')
            ->willReturn($visitorSegmentIds);
        $sessionMock->expects($this->once())
            ->method('setCustomerSegmentIds')
            ->with($resultSegmentIds);

        $this->httpContextMock->expects($this->once())
            ->method('setValue')
            ->with(\Magento\CustomerSegment\Helper\Data::CONTEXT_SEGMENT, $contextSegmentIds, $contextSegmentIds)
            ->willReturnSelf();

        $this->assertEquals(
            $this->model,
            $this->model->removeVisitorFromWebsiteSegments($sessionMock, $websiteId, $segmentIds)
        );
    }

    public function dataProviderRemoveVisitorFromWebsiteSegments()
    {
        return [
            ['', 1, [], [], []],
            [[1 => [2, 3], 2 => [4]], 1, [2, 5], [1 => [1 => 3], 2 => [4]], [1 => 3]],
            [[1 => [2, 3], 3 => [4]], 2, [2, 5], [1 => [2, 3], 3 => [4]], []],
            [[2 => [2, 3]], 2, [], [2 => [2, 3]], [2, 3]],
            [[2 => [2, 3]], 2, [2, 3], [2 => []], []],
        ];
    }
}
