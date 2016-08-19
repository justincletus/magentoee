<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftWrapping\Test\Unit\Model;

use Magento\Framework\Api;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Store\Model\Store;

class WrappingRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GiftWrapping\Model\WrappingRepository */
    protected $wrappingRepository;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $wrappingFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $collectionFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $searchResultFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $searchResultsMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $resourceMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $storeManagerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $wrappingCollectionMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $wrappingMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $storeMock;

    protected function setUp()
    {
        $this->wrappingFactoryMock = $this->getMock(
            'Magento\GiftWrapping\Model\WrappingFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->collectionFactoryMock = $this->getMock(
            'Magento\GiftWrapping\Model\ResourceModel\Wrapping\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->wrappingCollectionMock =
            $this->getMock('Magento\GiftWrapping\Model\ResourceModel\Wrapping\Collection', [], [], '', false);
        $methods = ['create'];
        $this->searchResultFactoryMock = $this->getMock(
            'Magento\GiftWrapping\Api\Data\WrappingSearchResultsInterfaceFactory',
            $methods,
            [],
            '',
            false
        );
        $this->searchResultsMock = $this->getMock(
            '\Magento\GiftWrapping\Api\Data\WrappingSearchResultsInterface',
            [],
            [],
            '',
            false
        );
        $this->resourceMock = $this->getMock('Magento\GiftWrapping\Model\ResourceModel\Wrapping', [], [], '', false);
        $this->storeManagerMock = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->wrappingMock= $this->getMock('Magento\GiftWrapping\Model\Wrapping', [], [], '', false);
        $this->storeMock =
            $this->getMock('Magento\Store\Model\Store', ['getBaseCurrencyCode', '__wakeUp'], [], '', false);
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->wrappingRepository = new \Magento\GiftWrapping\Model\WrappingRepository(
            $this->wrappingFactoryMock,
            $this->collectionFactoryMock,
            $this->searchResultFactoryMock,
            $this->resourceMock,
            $this->storeManagerMock
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetException()
    {
        list($id, $storeId) = [1, 1];
        /** @var \PHPUnit_Framework_MockObject_MockObject $wrappingMock */
        $wrappingMock = $this->getMock('Magento\GiftWrapping\Model\Wrapping', [], [], '', false);

        $this->wrappingFactoryMock->expects($this->once())->method('create')->will($this->returnValue($wrappingMock));
        $wrappingMock->expects($this->once())->method('setStoreId')->with($storeId);
        $this->resourceMock->expects($this->once())->method('load')->with($wrappingMock, $id);
        $wrappingMock->expects($this->once())->method('getId')->will($this->returnValue(null));

        $this->wrappingRepository->get($id, $storeId);
    }

    public function testGetSuccess()
    {
        list($id, $storeId) = [1, 1];
        /** @var \PHPUnit_Framework_MockObject_MockObject $wrappingMock */
        $wrappingMock = $this->getMock('Magento\GiftWrapping\Model\Wrapping', [], [], '', false);

        $this->wrappingFactoryMock->expects($this->once())->method('create')->will($this->returnValue($wrappingMock));
        $wrappingMock->expects($this->once())->method('setStoreId')->with($storeId);
        $this->resourceMock->expects($this->once())->method('load')->with($wrappingMock, $id);
        $wrappingMock->expects($this->once())->method('getId')->will($this->returnValue($id));

        $this->assertSame($wrappingMock, $this->wrappingRepository->get($id, $storeId));
    }

    public function testDelete()
    {
        $this->resourceMock->expects($this->once())->method('delete')->with($this->wrappingMock);
        $this->wrappingRepository->delete($this->wrappingMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedMessage Unable to remove gift wrapping 1
     */
    public function testDeleteWithException()
    {
        $this->wrappingMock->expects($this->once())->method('getWrappingId')->willReturn(1);
        $this->resourceMock
            ->expects($this->once())
            ->method('delete')
            ->with($this->wrappingMock)
            ->willThrowException(new \Exception());
        $this->wrappingRepository->delete($this->wrappingMock);
    }

    public function testDeleteById()
    {
        $id = 1;
        $this->wrappingFactoryMock->expects($this->once())->method('create')->willReturn($this->wrappingMock);
        $this->resourceMock->expects($this->once())->method('load')->with($this->wrappingMock, $id);
        $this->resourceMock->expects($this->once())->method('delete')->with($this->wrappingMock);
        $this->wrappingMock->expects($this->once())->method('getId')->willReturn($id);
        $this->assertTrue($this->wrappingRepository->deleteById($id));
    }

    public function testSave()
    {
        $imageContent = base64_encode('image content');
        $imageName = 'image.jpg';
        $this->wrappingMock
            ->expects($this->once())
            ->method('getImageBase64Content')
            ->will($this->returnValue($imageContent));
        $this->wrappingMock->expects($this->once())->method('getImageName')->will($this->returnValue($imageName));

        $this->wrappingMock->expects($this->once())->method('getWrappingId')->willReturn(null);
        $this->wrappingMock->expects($this->once())->method('getBaseCurrencyCode')->willReturn('USD');
        $this->wrappingMock->expects($this->once())->method('setStoreId')->with(Store::DEFAULT_STORE_ID);
        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($this->storeMock);
        $this->storeMock->expects($this->once())->method('getBaseCurrencyCode')->willReturn('USD');
        $this->resourceMock->expects($this->once())->method('save')->with($this->wrappingMock);
        $this->wrappingRepository->save($this->wrappingMock);
    }

    public function testUpdate()
    {
        $id = 1;
        $imageContent = base64_encode('image content');
        $imageName = 'image.jpg';
        $this->wrappingFactoryMock->expects($this->once())->method('create')->willReturn($this->wrappingMock);
        $this->resourceMock
            ->expects($this->once())
            ->method('load')
            ->with($this->wrappingMock, $id)
            ->willReturn($this->wrappingMock);
        $this->wrappingMock->expects($this->once())->method('getData')->willReturn(['data']);
        $this->wrappingMock->expects($this->once())->method('addData')->with(['data'])->willReturnSelf();
        $this->wrappingMock->expects($this->once())->method('getId')->willReturn($id);
        $this->wrappingMock
            ->expects($this->once())
            ->method('getImageBase64Content')
            ->will($this->returnValue($imageContent));
        $this->wrappingMock->expects($this->once())->method('getImageName')->will($this->returnValue($imageName));

        $this->wrappingMock->expects($this->once())->method('getWrappingId')->willReturn($id);
        $this->wrappingMock->expects($this->once())->method('getBaseCurrencyCode')->willReturn('USD');
        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($this->storeMock);
        $this->storeMock->expects($this->once())->method('getBaseCurrencyCode')->willReturn('USD');
        $this->resourceMock->expects($this->once())->method('save')->with($this->wrappingMock);

        $this->wrappingRepository->save($this->wrappingMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\StateException
     * @expectedMessage Please enter valid currency code: UA
     */
    public function testSaveWithInvalidCurrencyCode()
    {
        $id = 1;
        $this->resourceMock->expects($this->never())->method('load');
        $this->wrappingMock
            ->expects($this->never())
            ->method('getImageBase64Content');
        $this->wrappingMock->expects($this->once())->method('getWrappingId')->willReturn($id);
        $this->wrappingMock->expects($this->once())->method('getBaseCurrencyCode')->willReturn('USD');
        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($this->storeMock);
        $this->storeMock->expects($this->once())->method('getBaseCurrencyCode')->willReturn('UA');
        $this->wrappingRepository->save($this->wrappingMock);
    }


    public function testGetListStatusFilter()
    {
        $criteriaMock = $this->preparedCriteriaFilterMock('status');
        list($collectionMock) = $this->getPreparedCollectionAndItems();

        $collectionMock->expects($this->once())->method('applyStatusFilter');

        $this->searchResultsMock->expects($this->once())->method('setItems')->willReturnSelf();
        $this->searchResultsMock->expects($this->once())->method('setTotalCount')->willReturnSelf();
        $this->searchResultsMock->expects($this->once())->method('setSearchCriteria')->willReturnSelf();
        $this->searchResultFactoryMock->expects($this->once())->method('create')
            ->willReturn($this->searchResultsMock);
        $this->wrappingRepository->getList($criteriaMock);
    }

    public function testFindStoreIdFilter()
    {
        $criteriaMock = $this->preparedCriteriaFilterMock('store_id');
        list($collectionMock) = $this->getPreparedCollectionAndItems();


        $collectionMock->expects($this->once())->method('addStoreAttributesToResult')->with(0);
        $this->searchResultsMock->expects($this->once())->method('setItems')->willReturnSelf();
        $this->searchResultsMock->expects($this->once())->method('setTotalCount')->willReturnSelf();
        $this->searchResultsMock->expects($this->once())->method('setSearchCriteria')->willReturnSelf();
        $this->searchResultFactoryMock->expects($this->once())->method('create')
            ->willReturn($this->searchResultsMock);
        $this->wrappingRepository->getList($criteriaMock);
    }

    /**
     * @param string|null $condition
     * @param string $expectedCondition
     * @dataProvider conditionDataProvider
     */
    public function testFindByCondition($condition, $expectedCondition)
    {
        $field = 'condition';
        $criteriaMock = $this->preparedCriteriaFilterMock($field, $condition);
        list($collectionMock) = $this->getPreparedCollectionAndItems();

        $collectionMock->expects($this->once())->method('addFieldToFilter')->with(
            $field,
            [$expectedCondition => $field]
        );
        $this->searchResultsMock->expects($this->once())->method('setItems')->willReturnSelf();
        $this->searchResultsMock->expects($this->once())->method('setTotalCount')->willReturnSelf();
        $this->searchResultsMock->expects($this->once())->method('setSearchCriteria')->willReturnSelf();
        $this->searchResultFactoryMock->expects($this->once())->method('create')
            ->willReturn($this->searchResultsMock);
        $this->wrappingRepository->getList($criteriaMock);
    }

    /**
     * @return array
     */
    public function conditionDataProvider()
    {
        return [
            [null, 'eq'],
            ['not_eq', 'not_eq']
        ];
    }

    /**
     * Prepares mocks
     *
     * @param $filterType
     * @param string $condition
     * @return \Magento\Framework\Api\SearchCriteria|\PHPUnit_Framework_MockObject_MockObject
     */
    private function preparedCriteriaFilterMock($filterType, $condition = 'eq')
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $criteriaMock */
        $criteriaMock = $this->getMock('Magento\Framework\Api\SearchCriteria', [], [], '', false);
        /** @var \Magento\Framework\Api\Search\FilterGroup|\PHPUnit_Framework_MockObject_MockObject $filterGroup */
        $filterGroupMock = $this->getMock('Magento\Framework\Api\Search\FilterGroup', [], [], '', false);
        /** @var \Magento\Framework\Api\Filter|\PHPUnit_Framework_MockObject_MockObject $filterMock */
        $filterMock = $this->getMock('Magento\Framework\Api\Filter', [], [], '', false);

        $criteriaMock->expects($this->once())->method('getFilterGroups')->will($this->returnValue([$filterGroupMock]));
        $filterGroupMock->expects($this->once())->method('getFilters')->will($this->returnValue([$filterMock]));

        $filterMock->expects($this->any())->method('getConditionType')->will($this->returnValue($condition));
        $filterMock->expects($this->any())->method('getField')->will($this->returnValue($filterType));
        $filterMock->expects($this->any())->method('getValue')->will($this->returnValue($filterType));

        return $criteriaMock;
    }

    /**
     * Prepares collection
     * @return array
     */
    private function getPreparedCollectionAndItems()
    {
        $items = [new \Magento\Framework\DataObject()];
        $collectionMock =
            $this->getMock('Magento\GiftWrapping\Model\ResourceModel\Wrapping\Collection', [], [], '', false);

        $this->collectionFactoryMock->expects($this->once())->method('create')->will(
            $this->returnValue($collectionMock)
        );
        $collectionMock->expects($this->once())->method('addWebsitesToResult');
        $collectionMock->expects($this->once())->method('getItems')->will($this->returnValue($items));

        return [$collectionMock, $items];
    }
}
