<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Test\Unit\Model\Service;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class TrackManagementTest
 */
class TrackManagementTest extends \PHPUnit_Framework_TestCase
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
     * Label service
     *
     * @var \Magento\Rma\Model\Shipping\LabelService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $labelServiceMock;

    /**
     * RMA repository
     *
     * @var \Magento\Rma\Api\RmaRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaRepositoryMock;

    /**
     * Filter builder
     *
     * @var \Magento\Framework\Api\FilterBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterBuilderMock;

    /**
     * Criteria builder
     *
     * @var \Magento\Framework\Api\SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $criteriaBuilderMock;

    /**
     * Track repository
     *
     * @var \Magento\Rma\Api\TrackRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $trackRepositoryMock;

    /**
     * @var \Magento\Rma\Model\Service\TrackManagement
     */
    protected $trackManagement;

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
        $this->labelServiceMock = $this->getMock(
            'Magento\Rma\Model\Shipping\LabelService',
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
        $this->trackRepositoryMock = $this->getMockForAbstractClass(
            'Magento\Rma\Api\TrackRepositoryInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->criteriaBuilderMock = $this->getMock(
            'Magento\Framework\Api\SearchCriteriaBuilder',
            [],
            [],
            '',
            false
        );
        $this->filterBuilderMock = $this->getMock(
            'Magento\Framework\Api\FilterBuilder',
            [],
            [],
            '',
            false
        );

        $this->trackManagement = $this->objectManager->getObject(
            'Magento\Rma\Model\Service\TrackManagement',
            [
                'permissionChecker' => $this->permissionCheckerMock,
                'labelService' => $this->labelServiceMock,
                'rmaRepository' => $this->rmaRepositoryMock,
                'trackRepository' => $this->trackRepositoryMock,
                'criteriaBuilder' => $this->criteriaBuilderMock,
                'filterBuilder' => $this->filterBuilderMock,
            ]
        );
    }

    /**
     * Run test getShippingLabelPdf method
     *
     * @return void
     */
    public function testGetShippingLabelPdf()
    {
        $expectedResult = base64_encode('test-label');
        $rmaMock = $this->getMock(
            'Magento\Rma\Model\Rma',
            [],
            [],
            '',
            false
        );

        $this->permissionCheckerMock->expects($this->once())
            ->method('checkRmaForCustomerContext');
        $this->rmaRepositoryMock->expects($this->once())
            ->method('get')
            ->with(10)
            ->willReturn($rmaMock);
        $this->labelServiceMock->expects($this->once())
            ->method('getShippingLabelByRmaPdf')
            ->with($rmaMock)
            ->willReturn('test-label');
        $actualResult = $this->trackManagement->getShippingLabelPdf(10);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Run test getTracks method
     *
     * @return void
     */
    public function testGetTracks()
    {
        $filter = ['eq' => 'filter'];
        $criteriaMock = $this->getMock(
            'Magento\Framework\Api\SearchCriteria',
            [],
            [],
            '',
            false
        );
        $this->filterBuilderMock->expects($this->once())
            ->method('setField')
            ->with('rma_entity_id')
            ->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())
            ->method('setValue')
            ->with(10)
            ->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn('filter');

        $this->criteriaBuilderMock->expects($this->once())
            ->method('addFilters')
            ->with($filter);
        $this->criteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($criteriaMock);

        $this->trackRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($criteriaMock)
            ->willReturn('track-list');

        $this->assertEquals('track-list', $this->trackManagement->getTracks(10));
    }

    /**
     * Run test addTrack method
     *
     * @return void
     */
    public function testAddTrack()
    {
        $trackMock = $this->getMockForAbstractClass(
            'Magento\Rma\Api\Data\TrackInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $rmaMock = $this->getMock(
            'Magento\Rma\Model\Rma',
            [],
            [],
            '',
            false
        );

        $this->permissionCheckerMock->expects($this->once())
            ->method('isCustomerContext')
            ->willReturn(false);
        $this->rmaRepositoryMock->expects($this->once())
            ->method('get')
            ->with(10)
            ->willReturn($rmaMock);
        $rmaMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn(23);
        $trackMock->expects($this->once())
            ->method('setRmaEntityId')
            ->with(23);
        $this->trackRepositoryMock->expects($this->once())
            ->method('save')
            ->with($trackMock)
            ->willReturn(true);

        $this->assertTrue($this->trackManagement->addTrack(10, $trackMock));
    }

    /**
     * Run test addTrack method [Exception]
     *
     * @return void
     * @expectedException \Magento\Framework\Exception\StateException
     */
    public function testAddTrackException()
    {
        $trackMock = $this->getMockForAbstractClass(
            'Magento\Rma\Api\Data\TrackInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->permissionCheckerMock->expects($this->once())
            ->method('isCustomerContext')
            ->willReturn(true);

        $this->trackManagement->addTrack(10, $trackMock);
    }

    /**
     * Run test removeTrackById method
     *
     * @return void
     */
    public function testRemoveTrackById()
    {
        $filter = ['eq' => 'filter'];
        $criteriaMock = $this->getMock(
            'Magento\Framework\Api\SearchCriteria',
            [],
            [],
            '',
            false
        );
        $trackSearchResult = $this->getMockForAbstractClass(
            'Magento\Rma\Api\Data\TrackSearchResultInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $trackMock = $this->getMockForAbstractClass(
            'Magento\Rma\Api\Data\TrackInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $tracksMock = [$trackMock];

        $this->permissionCheckerMock->expects($this->once())
            ->method('isCustomerContext')
            ->willReturn(false);
        $this->filterBuilderMock->expects($this->atLeastOnce())
            ->method('setField')
            ->willReturnSelf();
        $this->filterBuilderMock->expects($this->atLeastOnce())
            ->method('setValue')
            ->willReturnSelf();
        $this->filterBuilderMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn('filter');
        $this->criteriaBuilderMock->expects($this->atLeastOnce())
            ->method('addFilters')
            ->with($filter);
        $this->criteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($criteriaMock);
        $this->trackRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($criteriaMock)
            ->willReturn($trackSearchResult);
        $trackSearchResult->expects($this->once())
            ->method('getItems')
            ->willReturn($tracksMock);
        $this->trackRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($trackMock)
            ->willReturn(['deleted']);

        $this->assertTrue($this->trackManagement->removeTrackById(10, 20));
    }

    /**
     * Run test removeTrackById method
     *
     * @return void
     * @expectedException \Magento\Framework\Exception\StateException
     */
    public function testRemoveTrackByIdException()
    {
        $this->permissionCheckerMock->expects($this->once())
            ->method('isCustomerContext')
            ->willReturn(true);

        $this->assertTrue($this->trackManagement->removeTrackById(10, 20));
    }
}
