<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Model\Hierarchy;

use Magento\VersionsCms\Model\Hierarchy\NodeRepository;
use Magento\Framework\Api\SortOrder;

/**
 * Test for Magento\VersionsCms\Model\Hierarchy\NodeRepository
 */
class NodeRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeRepository
     */
    protected $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\VersionsCms\Model\ResourceModel\Hierarchy\Node
     */
    protected $nodeResource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\VersionsCms\Model\Hierarchy\Node
     */
    protected $node;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\VersionsCms\Api\Data\HierarchyNodeInterface
     */
    protected $nodeData;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\VersionsCms\Api\Data\HierarchyNodeSearchResultsInterface
     */
    protected $nodeSearchResult;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Api\DataObjectHelper
     */
    protected $dataHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\VersionsCms\Model\ResourceModel\Hierarchy\Node\Collection
     */
    protected $collection;

    /**
     * Initialize repository
     */
    public function setUp()
    {
        $this->nodeResource = $this->getMockBuilder('Magento\VersionsCms\Model\ResourceModel\Hierarchy\Node')
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectProcessor = $this->getMockBuilder('Magento\Framework\Reflection\DataObjectProcessor')
            ->disableOriginalConstructor()
            ->getMock();
        $nodeFactory = $this->getMockBuilder('Magento\VersionsCms\Model\Hierarchy\NodeFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $nodeDataFactory = $this->getMockBuilder('Magento\VersionsCms\Api\Data\HierarchyNodeInterfaceFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $nodeSearchResultFactory = $this->getMockBuilder(
            'Magento\VersionsCms\Api\Data\HierarchyNodeSearchResultsInterfaceFactory'
        )->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $collectionFactory =
            $this->getMockBuilder('Magento\VersionsCms\Model\ResourceModel\Hierarchy\Node\CollectionFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->node = $this->getMockBuilder('Magento\VersionsCms\Model\Hierarchy\Node')
            ->disableOriginalConstructor()
            ->getMock();
        $this->nodeData = $this->getMockBuilder('Magento\VersionsCms\Api\DataHierarchyNodeInterface')
            ->getMock();
        $this->nodeSearchResult = $this->getMockBuilder(
            'Magento\VersionsCms\Api\Data\HierarchyNodeSearchResultsInterface'
        )->getMock();
        $this->collection = $this->getMockBuilder('Magento\VersionsCms\Model\ResourceModel\Hierarchy\Node\Collection')
            ->disableOriginalConstructor()
            ->setMethods([
                'addFieldToFilter',
                'getSize',
                'setCurPage',
                'setPageSize',
                'load',
                'addOrder',
                'addStoreFilter',
                'joinCmsPage',
                'joinMetaData',
                'addCmsPageInStoresColumn',
                'addLastChildSortOrderColumn',
            ])
            ->getMock();

        $nodeFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->node);
        $nodeDataFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->nodeData);
        $nodeSearchResultFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->nodeSearchResult);
        $collectionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->collection);
        /**
         * @var \Magento\VersionsCms\Model\Hierarchy\NodeFactory $nodeFactory
         * @var \Magento\VersionsCms\Api\Data\HierarchyNodeInterfaceFactory $nodeDataFactory
         * @var \Magento\VersionsCms\Api\Data\HierarchyNodeSearchResultsInterfaceFactory $nodeSearchResultFactory
         * @var \Magento\VersionsCms\Model\ResourceModel\Hierarchy\Node\CollectionFactory $collectionFactory
         */

        $this->dataHelper = $this->getMockBuilder('Magento\Framework\Api\DataObjectHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = new NodeRepository(
            $this->nodeResource,
            $nodeFactory,
            $nodeDataFactory,
            $collectionFactory,
            $nodeSearchResultFactory,
            $this->dataHelper,
            $this->dataObjectProcessor
        );
    }

    /**
     * @test
     */
    public function testSave()
    {
        $this->nodeResource->expects($this->once())
            ->method('save')
            ->with($this->node)
            ->willReturnSelf();
        $this->assertEquals($this->node, $this->repository->save($this->node));
    }

    /**
     * @test
     */
    public function testDeleteById()
    {
        $nodeId = '123';

        $this->node->expects($this->once())
            ->method('getId')
            ->willReturn(true);
        $this->nodeResource->expects($this->once())
            ->method('load')
            ->with($this->node, $nodeId)
            ->willReturn($this->node);
        $this->nodeResource->expects($this->once())
            ->method('delete')
            ->with($this->node)
            ->willReturnSelf();

        $this->assertTrue($this->repository->deleteById($nodeId));
    }

    /**
     * @test
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSaveException()
    {
        $this->nodeResource->expects($this->once())
            ->method('save')
            ->with($this->node)
            ->willThrowException(new \Exception());
        $this->repository->save($this->node);
    }

    /**
     * @test
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function testDeleteException()
    {
        $this->nodeResource->expects($this->once())
            ->method('delete')
            ->with($this->node)
            ->willThrowException(new \Exception());
        $this->repository->delete($this->node);
    }

    /**
     * @test
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetByIdException()
    {
        $nodeId = '123';

        $this->node->expects($this->once())
            ->method('getId')
            ->willReturn(false);
        $this->nodeResource->expects($this->once())
            ->method('load')
            ->with($this->node, $nodeId)
            ->willReturn($this->node);
        $this->repository->getById($nodeId);
    }

    /**
     * @test
     */
    public function testGetList()
    {
        $field = 'name';
        $value = 'magento';
        $condition = 'eq';
        $total = 10;
        $currentPage = 3;
        $pageSize = 2;
        $sortField = 'id';

        $criteria = $this->getMockBuilder('Magento\Framework\Api\SearchCriteriaInterface')->getMock();
        $filterGroup = $this->getMockBuilder('Magento\Framework\Api\Search\FilterGroup')->getMock();
        $filter = $this->getMockBuilder('Magento\Framework\Api\Filter')->getMock();
        $storeFilter = $this->getMockBuilder('Magento\Framework\Api\Filter')->getMock();
        $sortOrder = $this->getMockBuilder('Magento\Framework\Api\SortOrder')->getMock();

        $criteria->expects($this->once())->method('getFilterGroups')->willReturn([$filterGroup]);
        $criteria->expects($this->once())->method('getSortOrders')->willReturn([$sortOrder]);
        $criteria->expects($this->once())->method('getCurrentPage')->willReturn($currentPage);
        $criteria->expects($this->once())->method('getPageSize')->willReturn($pageSize);
        $filterGroup->expects($this->once())->method('getFilters')->willReturn([$storeFilter, $filter]);
        $filter->expects($this->once())->method('getConditionType')->willReturn($condition);
        $filter->expects($this->any())->method('getField')->willReturn($field);
        $filter->expects($this->once())->method('getValue')->willReturn($value);
        $storeFilter->expects($this->any())->method('getField')->willReturn('store_id');
        $storeFilter->expects($this->once())->method('getValue')->willReturn(1);
        $sortOrder->expects($this->once())->method('getField')->willReturn($sortField);
        $sortOrder->expects($this->once())->method('getDirection')->willReturn(SortOrder::SORT_DESC);

        /** @var \Magento\Framework\Api\SearchCriteriaInterface $criteria */

        $this->collection->addItem($this->node);
        $this->nodeSearchResult->expects($this->once())->method('setSearchCriteria')->with($criteria)->willReturnSelf();
        $this->collection->expects($this->once())->method('joinCmsPage')->willReturnSelf();
        $this->collection->expects($this->once())->method('joinMetaData')->willReturnSelf();
        $this->collection->expects($this->once())->method('addCmsPageInStoresColumn')->willReturnSelf();
        $this->collection->expects($this->once())->method('addLastChildSortOrderColumn')->willReturnSelf();
        $this->collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with($field, [$condition => $value])
            ->willReturnSelf();
        $this->nodeSearchResult->expects($this->once())->method('setTotalCount')->with($total)->willReturnSelf();
        $this->collection->expects($this->once())->method('getSize')->willReturn($total);
        $this->collection->expects($this->once())->method('setCurPage')->with($currentPage)->willReturnSelf();
        $this->collection->expects($this->once())->method('setPageSize')->with($pageSize)->willReturnSelf();
        $this->collection->expects($this->once())->method('addOrder')->with($sortField, 'DESC')->willReturnSelf();
        $this->node->expects($this->once())->method('getData')->willReturn(['data']);
        $this->nodeSearchResult->expects($this->once())->method('setItems')->with(['someData'])->willReturnSelf();
        $this->dataHelper->expects($this->once())
            ->method('populateWithArray');
        $this->dataObjectProcessor->expects($this->once())
            ->method('buildOutputDataArray')
            ->willReturn('someData');

        $this->assertEquals($this->nodeSearchResult, $this->repository->getList($criteria));
    }
}
