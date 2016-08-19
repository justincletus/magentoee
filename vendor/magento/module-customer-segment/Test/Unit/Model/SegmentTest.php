<?php
/**
 * Unit test for CustomerSegment \Magento\CustomerSegment\Model\Segment
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\CustomerSegment\Model\Segment testing
 */
namespace Magento\CustomerSegment\Test\Unit\Model;

class SegmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerSegment\Model\Segment
     */
    protected $model;

    /**
     * @var \Magento\Store\Model\StoreManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Quote\Model\QueryResolver| \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queryResolverMock;

    /**
     * @var \Magento\CustomerSegment\Model\ResourceModel\Segment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    protected function setUp()
    {
        $this->storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', [], [], '', false);
        $this->queryResolverMock = $this->getMock('Magento\Quote\Model\QueryResolver', [], [], '', false);
        $this->resourceMock = $this->getMock('Magento\CustomerSegment\Model\ResourceModel\Segment', [], [], '', false);

        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $helper->getObject(
            '\Magento\CustomerSegment\Model\Segment',
            [
                'storeManager' => $this->storeManagerMock,
                'queryResolver' => $this->queryResolverMock,
                'resource' => $this->resourceMock
            ]
        );
    }

    protected function tearDown()
    {
        $this->model = null;
        $this->storeManagerMock = null;
        $this->queryResolverMock = null;
        $this->resourceMock = null;
    }

    /**
     * @param array $websiteData
     * @param null|int $withParam
     */
    protected function prepareWebsite($websiteData = [], $withParam = null)
    {
        $website = new \Magento\Framework\DataObject($websiteData);
        $this->storeManagerMock
            ->expects($this->once())
            ->method('getWebsite')
            ->with($withParam)
            ->willReturn($website);
    }

    public function testValidateCustomerWithEmptyQuery()
    {
        $this->prepareWebsite();
        $this->assertFalse($this->model->validateCustomer(null, null));
    }

    public function testValidateCustomerForVisitor()
    {
        $this->prepareWebsite(['id' => 1], 1);

        $sql = 'select :quote_id :visitor_id';
        $this->model->setData('condition_sql', $sql);
        $this->model->setVisitorId('visitor_1');
        $this->model->setQuoteId('quote_1');

        $conditions = $this->getMockBuilder('Magento\Rule\Model\Condition\Combine')
            ->disableOriginalConstructor()
            ->setMethods(['isSatisfiedBy'])
            ->getMock();
        $params = [
            'quote_id' => 'quote_1',
            'visitor_id' => 'visitor_1',
        ];
        $conditions->expects($this->once())
            ->method('isSatisfiedBy')
            ->with($this->equalTo(null), $this->equalTo(1), $this->equalTo($params))
            ->willReturn(true);
        $this->model->setConditions($conditions);
        $this->assertTrue($this->model->validateCustomer(null, 1));
    }
}
