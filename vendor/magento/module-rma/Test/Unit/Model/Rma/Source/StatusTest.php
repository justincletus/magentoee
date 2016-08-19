<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Test\Unit\Model\Rma\Source;

use Magento\Rma\Model\Rma\Source\Status;
use Magento\Rma\Model\Item\Attribute\Source\Status as ItemAttributeStatus;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Model\Item\Attribute\Source\StatusFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrOptionCollectionFactoryMock;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrOptionFactoryMock;

    /**
     * @var Status
     */
    protected $status;

    public function setUp()
    {
        $this->statusFactoryMock = $this->getMock(
            'Magento\Rma\Model\Item\Attribute\Source\StatusFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->attrOptionCollectionFactoryMock = $this->getMock(
            'Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->attrOptionFactoryMock = $this->getMock(
            'Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->status = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))->getObject(
            'Magento\Rma\Model\Rma\Source\Status',
            [
                'attrOptionCollectionFactory' => $this->attrOptionCollectionFactoryMock,
                'attrOptionFactory' => $this->attrOptionFactoryMock,
                'statusFactory' => $this->statusFactoryMock,
            ]
        );
    }

    /**
     * @dataProvider itemLabelDataProvider
     * @param string $state
     * @param string $expected
     */
    public function testGetItemLabel($state, $expected)
    {
        $this->assertEquals($expected, $this->status->getItemLabel($state));
    }

    /**
     * @dataProvider statusByItemsDataProvider
     * @param array $itemStatusArray
     * @param string $expected
     */
    public function testGetStatusByItems($itemStatusArray, $expected)
    {
        $itemStatusModelMock = $this->getMock(
            'Magento\Rma\Model\Item\Attribute\Source\Status',
            [],
            [],
            '',
            false
        );
        $this->statusFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($itemStatusModelMock));
        $itemStatusModelMock->expects($this->any())
            ->method('checkStatus')
            ->willReturn(true);

        $this->assertEquals($expected, $this->status->getStatusByItems($itemStatusArray));
    }

    /**
     * @dataProvider statusByItemsExceptionDataProvider
     * @param mixed $itemStatusArray
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage This is the wrong RMA item status.
     */
    public function testGetStatusByItemsException($itemStatusArray)
    {
        $itemStatusModelMock = $this->getMock(
            'Magento\Rma\Model\Item\Attribute\Source\Status',
            [],
            [],
            '',
            false
        );
        $this->statusFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($itemStatusModelMock));
        $itemStatusModelMock->expects($this->any())
            ->method('checkStatus')
            ->willReturn(false);

        $this->assertNull($this->status->getStatusByItems($itemStatusArray));
    }

    /**
     * @dataProvider buttonDisabledStatusDataProvider
     * @param string $status
     * @param bool $expected
     */
    public function testGetButtonDisabledStatus($status, $expected)
    {
        $this->assertEquals($expected, $this->status->getButtonDisabledStatus($status));
    }

    public function statusByItemsDataProvider()
    {
        return [
            [
                ['item1' => ItemAttributeStatus::STATE_PENDING, 'item2' => ItemAttributeStatus::STATE_PENDING],
                Status::STATE_PENDING,
            ],
            [
                ['item1' => ItemAttributeStatus::STATE_AUTHORIZED],
                Status::STATE_AUTHORIZED
            ],
            [
                ['item1' => ItemAttributeStatus::STATE_DENIED],
                Status::STATE_CLOSED],
            [
                ['item1' => ItemAttributeStatus::STATE_DENIED, 'item2' => ItemAttributeStatus::STATE_PENDING],
                Status::STATE_PENDING
            ],
            [
                ['item1' => ItemAttributeStatus::STATE_PENDING, 'item2' => ItemAttributeStatus::STATE_AUTHORIZED],
                Status::STATE_PARTIAL_AUTHORIZED
            ],
            [
                ['item1' => ItemAttributeStatus::STATE_RECEIVED],
                Status::STATE_RECEIVED
            ],
            [
                ['item1' => ItemAttributeStatus::STATE_RECEIVED, 'item2' => ItemAttributeStatus::STATE_PENDING],
                Status::STATE_RECEIVED_ON_ITEM
            ],
            [
                ['item1' => ItemAttributeStatus::STATE_APPROVED],
                Status::STATE_PROCESSED_CLOSED
            ],
            [
                ['item1' => ItemAttributeStatus::STATE_DENIED, 'item2' => ItemAttributeStatus::STATE_APPROVED],
                Status::STATE_PROCESSED_CLOSED

            ],
            [
                ['item1' => ItemAttributeStatus::STATE_PENDING, 'item2' => ItemAttributeStatus::STATE_APPROVED],
                Status::STATE_APPROVED_ON_ITEM
            ],
            [
                ['item1' => ItemAttributeStatus::STATE_REJECTED],
                Status::STATE_CLOSED
            ],
            [
                ['item1' => ItemAttributeStatus::STATE_DENIED, 'item2' => ItemAttributeStatus::STATE_REJECTED],
                Status::STATE_CLOSED
            ],
            [
                [
                    'item1' => ItemAttributeStatus::STATE_PENDING, 'item2' => ItemAttributeStatus::STATE_REJECTED,
                ],
                Status::STATE_REJECTED_ON_ITEM
            ],
            [
                [
                    'item1' => ItemAttributeStatus::STATE_APPROVED, 'item2' => ItemAttributeStatus::STATE_REJECTED,
                ],
                Status::STATE_PROCESSED_CLOSED
            ],
            [
                [
                    'item1' => ItemAttributeStatus::STATE_APPROVED, 'item2' => ItemAttributeStatus::STATE_REJECTED,
                    'item3' => ItemAttributeStatus::STATE_DENIED,
                ],
                Status::STATE_PROCESSED_CLOSED
            ],
            [
                [
                    'item1' => ItemAttributeStatus::STATE_APPROVED, 'item2' => ItemAttributeStatus::STATE_REJECTED,
                    'item3' => ItemAttributeStatus::STATE_PENDING,
                ],
                Status::STATE_APPROVED_ON_ITEM
            ]
        ];
    }

    public function itemLabelDataProvider()
    {
        $state = 'Test_state';
        return [
            [Status::STATE_PENDING, 'Pending'],
            [Status::STATE_AUTHORIZED, 'Authorized'],
            [Status::STATE_PARTIAL_AUTHORIZED, 'Partially Authorized'],
            [Status::STATE_RECEIVED, 'Return Received'],
            [Status::STATE_RECEIVED_ON_ITEM, 'Return Partially Received'],
            [Status::STATE_APPROVED, 'Approved'],
            [Status::STATE_APPROVED_ON_ITEM, 'Partially Approved'],
            [Status::STATE_REJECTED, 'Rejected'],
            [Status::STATE_REJECTED_ON_ITEM, 'Partially Rejected'],
            [Status::STATE_DENIED, 'Denied'],
            [Status::STATE_CLOSED, 'Closed'],
            [Status::STATE_PROCESSED_CLOSED, 'Processed and Closed'],
            [$state, 'Test_state']
        ];
    }

    public function statusByItemsExceptionDataProvider()
    {
        return [
            ['test_not_array'],
            [
                []
            ],
            [
                ['item1' => 'WRONG_TEST_STATUS']
            ],
        ];
    }

    public function buttonDisabledStatusDataProvider()
    {
        return [
            [Status::STATE_PARTIAL_AUTHORIZED, true],
            [Status::STATE_RECEIVED, true],
            [Status::STATE_RECEIVED_ON_ITEM, true],
            [Status::STATE_APPROVED_ON_ITEM, true],
            [Status::STATE_REJECTED_ON_ITEM, true],
            [Status::STATE_CLOSED, true],
            [Status::STATE_PROCESSED_CLOSED, true],
            ['TEST_STATUS', false]
        ];
    }
}
