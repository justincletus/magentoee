<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Model\Page;

use Magento\VersionsCms\Model\Page\VersionRepository;
use Magento\Framework\Api\SortOrder;

/**
 * Test for Magento\VersionsCms\Model\Page\VersionRepository
 */
class VersionRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var VersionRepository
     */
    protected $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\VersionsCms\Model\ResourceModel\Page\Version
     */
    protected $versionResource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\VersionsCms\Model\Page\Version
     */
    protected $version;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\VersionsCms\Api\Data\PageVersionInterface
     */
    protected $versionData;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\VersionsCms\Api\Data\PageVersionSearchResultsInterface
     */
    protected $versionSearchResult;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Api\DataObjectHelper
     */
    protected $dataHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\VersionsCms\Model\ResourceModel\Page\Version\Collection
     */
    protected $collection;

    /**
     * Initialize repository
     */
    public function setUp()
    {
        $this->versionResource = $this->getMockBuilder('Magento\VersionsCms\Model\ResourceModel\Page\Version')
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectProcessor = $this->getMockBuilder('Magento\Framework\Reflection\DataObjectProcessor')
            ->disableOriginalConstructor()
            ->getMock();
        $versionFactory = $this->getMockBuilder('Magento\VersionsCms\Model\Page\VersionFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $versionDataFactory = $this->getMockBuilder('Magento\VersionsCms\Api\Data\PageVersionInterfaceFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $versionSearchResultFactory = $this->getMockBuilder(
            'Magento\VersionsCms\Api\Data\PageVersionSearchResultsInterfaceFactory'
        )->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $collectionFactory =
            $this->getMockBuilder('Magento\VersionsCms\Model\ResourceModel\Page\Version\CollectionFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->version = $this->getMockBuilder('Magento\VersionsCms\Model\Page\Version')
            ->disableOriginalConstructor()
            ->getMock();
        $this->versionData = $this->getMockBuilder('Magento\VersionsCms\Api\Data\PageVersionInterface')
            ->getMock();
        $this->versionSearchResult = $this->getMockBuilder(
            'Magento\VersionsCms\Api\Data\PageVersionSearchResultsInterface'
        )->getMock();
        $this->collection = $this->getMockBuilder('Magento\VersionsCms\Model\ResourceModel\Page\Version\Collection')
            ->disableOriginalConstructor()
            ->setMethods([
                'addFieldToFilter',
                'getSize',
                'setCurPage',
                'setPageSize',
                'load',
                'addOrder',
                'joinRevisions',
            ])
            ->getMock();

        $versionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->version);
        $versionDataFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->versionData);
        $versionSearchResultFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->versionSearchResult);
        $collectionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->collection);
        /**
         * @var \Magento\VersionsCms\Model\Page\VersionFactory $versionFactory
         * @var \Magento\VersionsCms\Api\Data\PageVersionInterfaceFactory $versionDataFactory
         * @var \Magento\VersionsCms\Api\Data\PageVersionSearchResultsInterfaceFactory $versionSearchResultFactory
         * @var \Magento\VersionsCms\Model\ResourceModel\Page\Version\CollectionFactory $collectionFactory
         */

        $this->dataHelper = $this->getMockBuilder('Magento\Framework\Api\DataObjectHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = new VersionRepository(
            $this->versionResource,
            $versionFactory,
            $versionDataFactory,
            $collectionFactory,
            $versionSearchResultFactory,
            $this->dataHelper,
            $this->dataObjectProcessor
        );
    }

    /**
     * @test
     */
    public function testSave()
    {
        $this->versionResource->expects($this->once())
            ->method('save')
            ->with($this->version)
            ->willReturnSelf();
        $this->assertEquals($this->version, $this->repository->save($this->version));
    }

    /**
     * @test
     */
    public function testDeleteById()
    {
        $versionId = '123';

        $this->version->expects($this->once())
            ->method('getId')
            ->willReturn(true);
        $this->versionResource->expects($this->once())
            ->method('load')
            ->with($this->version, $versionId)
            ->willReturn($this->version);
        $this->versionResource->expects($this->once())
            ->method('delete')
            ->with($this->version)
            ->willReturnSelf();

        $this->assertTrue($this->repository->deleteById($versionId));
    }

    /**
     * @test
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSaveException()
    {
        $this->versionResource->expects($this->once())
            ->method('save')
            ->with($this->version)
            ->willThrowException(new \Exception());
        $this->repository->save($this->version);
    }

    /**
     * @test
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function testDeleteException()
    {
        $this->versionResource->expects($this->once())
            ->method('delete')
            ->with($this->version)
            ->willThrowException(new \Exception());
        $this->repository->delete($this->version);
    }

    /**
     * @test
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetByIdException()
    {
        $versionId = '123';

        $this->version->expects($this->once())
            ->method('getId')
            ->willReturn(false);
        $this->versionResource->expects($this->once())
            ->method('load')
            ->with($this->version, $versionId)
            ->willReturn($this->version);
        $this->repository->getById($versionId);
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
            ->willReturn([$filter]);
        $filter->expects($this->once())
            ->method('getConditionType')
            ->willReturn($condition);
        $filter->expects($this->any())
            ->method('getField')
            ->willReturn($field);
        $filter->expects($this->once())
            ->method('getValue')
            ->willReturn($value);
        $sortOrder->expects($this->once())
            ->method('getField')
            ->willReturn($sortField);
        $sortOrder->expects($this->once())
            ->method('getDirection')
            ->willReturn(SortOrder::SORT_DESC);

        /** @var \Magento\Framework\Api\SearchCriteriaInterface $criteria */

        $this->collection->addItem($this->version);
        $this->versionSearchResult->expects($this->once())
            ->method('setSearchCriteria')
            ->with($criteria)
            ->willReturnSelf();
        $this->collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with($field, [$condition => $value])
            ->willReturnSelf();
        $this->versionSearchResult->expects($this->once())
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
        $this->version->expects($this->once())
            ->method('getData')
            ->willReturn(['data']);
        $this->versionSearchResult->expects($this->once())
            ->method('setItems')
            ->with(['someData'])
            ->willReturnSelf();
        $this->dataHelper->expects($this->once())
            ->method('populateWithArray');
        $this->dataObjectProcessor->expects($this->once())
            ->method('buildOutputDataArray')
            ->willReturn('someData');

        $this->assertEquals($this->versionSearchResult, $this->repository->getList($criteria));
    }
}
