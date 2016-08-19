<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Model\Page;

use Magento\VersionsCms\Model\Page\RevisionRepository;
use Magento\Framework\Api\SortOrder;

/**
 * Test for Magento\VersionsCms\Model\Page\RevisionRepository
 */
class RevisionRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RevisionRepository
     */
    protected $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\VersionsCms\Model\ResourceModel\Page\Revision
     */
    protected $revisionResource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\VersionsCms\Model\Page\Revision
     */
    protected $revision;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    protected $revisionData;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\VersionsCms\Api\Data\PageRevisionSearchResultsInterface
     */
    protected $revisionSearchResult;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Api\DataObjectHelper
     */
    protected $dataHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\VersionsCms\Model\ResourceModel\Page\Revision\Collection
     */
    protected $collection;

    /**
     * Initialize repository
     */
    public function setUp()
    {
        $this->revisionResource = $this->getMockBuilder('Magento\VersionsCms\Model\ResourceModel\Page\Revision')
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectProcessor = $this->getMockBuilder('Magento\Framework\Reflection\DataObjectProcessor')
            ->disableOriginalConstructor()
            ->getMock();
        $revisionFactory = $this->getMockBuilder('Magento\VersionsCms\Model\Page\RevisionFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $revisionDataFactory = $this->getMockBuilder('Magento\VersionsCms\Api\Data\PageRevisionInterfaceFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $revisionSearchResultFactory = $this->getMockBuilder(
            'Magento\VersionsCms\Api\Data\PageRevisionSearchResultsInterfaceFactory'
        )->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $collectionFactory =
            $this->getMockBuilder('Magento\VersionsCms\Model\ResourceModel\Page\Revision\CollectionFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->revision = $this->getMockBuilder('Magento\VersionsCms\Model\Page\Revision')
            ->disableOriginalConstructor()
            ->getMock();
        $this->revisionData = $this->getMockBuilder('Magento\VersionsCms\Api\Data\Page\RevisionInterface')
            ->getMock();
        $this->revisionSearchResult = $this->getMockBuilder(
            'Magento\VersionsCms\Api\Data\PageRevisionSearchResultsInterface'
        )->getMock();
        $this->collection = $this->getMockBuilder('Magento\VersionsCms\Model\ResourceModel\Page\Revision\Collection')
            ->disableOriginalConstructor()
            ->setMethods([
                'addFieldToFilter',
                'addVersionFilter',
                'getSize',
                'setCurPage',
                'setPageSize',
                'load',
                'addOrder',
                'joinVersions',
            ])
            ->getMock();

        $revisionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->revision);
        $revisionDataFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->revisionData);
        $revisionSearchResultFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->revisionSearchResult);
        $collectionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->collection);
        /**
         * @var \Magento\VersionsCms\Model\Page\RevisionFactory $revisionFactory
         * @var \Magento\VersionsCms\Api\Data\PageRevisionInterfaceFactory $revisionDataFactory
         * @var \Magento\VersionsCms\Api\Data\PageRevisionSearchResultsInterfaceFactory $revisionSearchResultFactory
         * @var \Magento\VersionsCms\Model\ResourceModel\Page\Revision\CollectionFactory $collectionFactory
         */

        $this->dataHelper = $this->getMockBuilder('Magento\Framework\Api\DataObjectHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = new RevisionRepository(
            $this->revisionResource,
            $revisionFactory,
            $revisionDataFactory,
            $collectionFactory,
            $revisionSearchResultFactory,
            $this->dataHelper,
            $this->dataObjectProcessor
        );
    }

    /**
     * @test
     */
    public function testSave()
    {
        $this->revisionResource->expects($this->once())
            ->method('save')
            ->with($this->revision)
            ->willReturnSelf();
        $this->assertEquals($this->revision, $this->repository->save($this->revision));
    }

    /**
     * @test
     */
    public function testDeleteById()
    {
        $revisionId = '123';

        $this->revision->expects($this->once())
            ->method('getId')
            ->willReturn(true);
        $this->revisionResource->expects($this->once())
            ->method('load')
            ->with($this->revision, $revisionId)
            ->willReturn($this->revision);
        $this->revisionResource->expects($this->once())
            ->method('delete')
            ->with($this->revision)
            ->willReturnSelf();

        $this->assertTrue($this->repository->deleteById($revisionId));
    }

    /**
     * @test
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSaveException()
    {
        $this->revisionResource->expects($this->once())
            ->method('save')
            ->with($this->revision)
            ->willThrowException(new \Exception());
        $this->repository->save($this->revision);
    }

    /**
     * @test
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function testDeleteException()
    {
        $this->revisionResource->expects($this->once())
            ->method('delete')
            ->with($this->revision)
            ->willThrowException(new \Exception());
        $this->repository->delete($this->revision);
    }

    /**
     * @test
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetByIdException()
    {
        $revisionId = '123';

        $this->revision->expects($this->once())
            ->method('getId')
            ->willReturn(false);
        $this->revisionResource->expects($this->once())
            ->method('load')
            ->with($this->revision, $revisionId)
            ->willReturn($this->revision);
        $this->repository->getById($revisionId);
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
        $versionFilter = $this->getMockBuilder('Magento\Framework\Api\Filter')->getMock();
        $sortOrder = $this->getMockBuilder('Magento\Framework\Api\SortOrder')->getMock();

        $criteria->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroup]);
        $criteria->expects($this->once())
            ->method('getSortOrders')
            ->willReturn([$sortOrder]);
        $criteria->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn($currentPage);
        $criteria->expects($this->once())
            ->method('getPageSize')
            ->willReturn($pageSize);
        $filterGroup->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filter, $versionFilter]);
        $filter->expects($this->once())
            ->method('getConditionType')
            ->willReturn($condition);
        $filter->expects($this->any())
            ->method('getField')
            ->willReturn($field);
        $filter->expects($this->once())
            ->method('getValue')
            ->willReturn($value);
        $versionFilter->expects($this->any())
            ->method('getField')
            ->willReturn('version_id');
        $versionFilter->expects($this->once())
            ->method('getValue')
            ->willReturn(1);
        $sortOrder->expects($this->once())
            ->method('getField')
            ->willReturn($sortField);
        $sortOrder->expects($this->once())
            ->method('getDirection')
            ->willReturn(SortOrder::SORT_DESC);

        /** @var \Magento\Framework\Api\SearchCriteriaInterface $criteria */

        $this->collection->addItem($this->revision);
        $this->revisionSearchResult->expects($this->once())
            ->method('setSearchCriteria')
            ->with($criteria)
            ->willReturnSelf();
        $this->collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with($field, [$condition => $value])
            ->willReturnSelf();
        $this->revisionSearchResult->expects($this->once())
            ->method('setTotalCount')
            ->with($total)
            ->willReturnSelf();
        $this->collection->expects($this->once())
            ->method('getSize')
            ->willReturn($total);
        $this->collection->expects($this->once())
            ->method('setCurPage')
            ->with($currentPage)
            ->willReturnSelf();
        $this->collection->expects($this->once())
            ->method('setPageSize')
            ->with($pageSize)
            ->willReturnSelf();
        $this->collection->expects($this->once())
            ->method('addOrder')
            ->with($sortField, 'DESC')
            ->willReturnSelf();
        $this->revision->expects($this->once())
            ->method('getData')
            ->willReturn(['data']);
        $this->revisionSearchResult->expects($this->once())
            ->method('setItems')
            ->with(['someData'])
            ->willReturnSelf();
        $this->dataHelper->expects($this->once())
            ->method('populateWithArray');
        $this->dataObjectProcessor->expects($this->once())
            ->method('buildOutputDataArray')
            ->willReturn('someData');

        $this->assertEquals($this->revisionSearchResult, $this->repository->getList($criteria));
    }
}
