<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Test\Unit\Model\Service;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class RmaManagementTest
 */
class RmaManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * Permission checker
     *
     * @var \Magento\Rma\Model\Rma\PermissionChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $permissionCheckerMock;

    /**
     * Rma repository
     *
     * @var \Magento\Rma\Api\RmaRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaRepositoryMock;

    /**
     * User context
     *
     * @var \Magento\Authorization\Model\UserContextInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userContextMock;

    /**
     * Filter builder
     *
     * @var \Magento\Framework\Api\FilterBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterBuilderMock;

    /**
     * Search criteria builder
     *
     * @var \Magento\Framework\Api\SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $criteriaBuilderMock;

    /**
     * @var \Magento\Rma\Model\Service\RmaManagement
     */
    protected $rmaManagement;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->permissionCheckerMock = $this->getMock(
            'Magento\Rma\Model\Rma\PermissionChecker',
            [],
            [],
            '',
            false
        );
        $this->rmaRepositoryMock = $this->getMockForAbstractClass(
            'Magento\Rma\Api\RmaRepositoryInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->userContextMock = $this->getMockForAbstractClass(
            'Magento\Authorization\Model\UserContextInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->filterBuilderMock = $this->getMock(
            'Magento\Framework\Api\FilterBuilder',
            [],
            [],
            '',
            false
        );
        $this->criteriaBuilderMock = $this->getMock(
            'Magento\Framework\Api\SearchCriteriaBuilder',
            [],
            [],
            '',
            false
        );

        $this->rmaManagement = $this->objectManager->getObject(
            'Magento\Rma\Model\Service\RmaManagement',
            [
                'permissionChecker' => $this->permissionCheckerMock,
                'rmaRepository' => $this->rmaRepositoryMock,
                'userContext' => $this->userContextMock,
                'filterBuilder' => $this->filterBuilderMock,
                'criteriaBuilder' => $this->criteriaBuilderMock,
            ]
        );
    }

    /**
     * Run test saveRma method
     *
     * @return void
     */
    public function testSaveRma()
    {
        $rmaMock = $this->getMockForAbstractClass(
            'Magento\Rma\Api\Data\RmaInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );

        $this->permissionCheckerMock->expects($this->once())
            ->method('checkRmaForCustomerContext');
        $this->rmaRepositoryMock->expects($this->once())
            ->method('save')
            ->with($rmaMock)
            ->willReturn(true);

        $this->assertTrue($this->rmaManagement->saveRma($rmaMock));
    }

    /**
     * Run test search method
     *
     * @return void
     */
    public function testSearch()
    {
        $expectedResult = 'test-result';
        $filterGroupMock = $this->getMock(
            'Magento\Framework\Api\Search\FilterGroup',
            [],
            [],
            '',
            false
        );
        $filterMock = $this->getMock(
            'Magento\Framework\Api\Filter',
            [],
            [],
            '',
            false
        );
        $searchCriteriaMock = $this->getMock(
            'Magento\Framework\Api\SearchCriteria',
            [],
            [],
            '',
            false
        );
        $searchCriteriaResultMock = $this->getMock(
            'Magento\Framework\Api\SearchCriteria',
            [],
            [],
            '',
            false
        );

        $this->permissionCheckerMock->expects($this->once())
            ->method('checkRmaForCustomerContext');
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupMock]);
        $filterGroupMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filterMock]);
        $filterMock->expects($this->once())
            ->method('getConditionType')
            ->willReturn('eq');
        $this->criteriaBuilderMock->expects($this->atLeastOnce())
            ->method('addFilters')
            ->with(['eq' => $filterMock]);
        $this->filterBuilderMock->expects($this->once())
            ->method('setField')
            ->willReturnSelf();
        $this->userContextMock->expects($this->once())
            ->method('getUserId')
            ->willReturn(12);
        $this->filterBuilderMock->expects($this->once())
            ->method('setValue')
            ->with(12)
            ->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);
        $this->criteriaBuilderMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($searchCriteriaResultMock);
        $this->rmaRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaResultMock)
            ->willReturn($expectedResult);

        $this->assertEquals($expectedResult, $this->rmaManagement->search($searchCriteriaMock));
    }
}
