<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Test\Unit\Model;

use Magento\GiftRegistry\Model\ShippingMethodManagement;

class ShippingMethodManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Model\ShippingMethodManagement
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $entityFactoryMock;

    /**
     * Shipping method management
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $methodManagementMock;

    /**
     * Estimated address factory
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $addressFactoryMock;

    protected function setUp()
    {
        $this->entityFactoryMock = $this->getMock(
            '\Magento\GiftRegistry\Model\EntityFactory',
            ['create', '__wakeup'],
            [],
            '',
            false
        );
        $this->addressFactoryMock = $this->getMock(
            '\Magento\Quote\Api\Data\EstimateAddressInterfaceFactory',
            ['create', '__wakeup'],
            [],
            '',
            false
        );
        $this->methodManagementMock = $this->getMock('\Magento\Quote\Api\ShippingMethodManagementInterface');
        $this->model = new ShippingMethodManagement(
            $this->entityFactoryMock,
            $this->methodManagementMock,
            $this->addressFactoryMock
        );
    }

    /**
     * @covers \Magento\GiftRegistry\Model\ShippingMethodManagement::estimateByRegistryId
     */
    public function testEstimateByRegistryId()
    {
        $cartId = 1;
        $giftRegistryId = 1;

        $giftRegistry = $this->getMock('\Magento\GiftRegistry\Model\Entity', [], [], '', false);
        $this->entityFactoryMock->expects($this->once())->method('create')->willReturn($giftRegistry);
        $giftRegistry->expects($this->any())->method('getId')->willReturn($giftRegistryId);
        $giftRegistry->expects($this->once())->method('loadByEntityItem')->with($giftRegistryId);
        $giftRegistry->expects($this->any())->method('getId')->willReturn($giftRegistryId);

        $customerAddress = $this->getMock('\Magento\Customer\Model\Address', [], [], '', false);
        $giftRegistry->expects($this->once())->method('exportAddress')->willReturn($customerAddress);

        $estimatedAddress = $this->getMock('\Magento\Quote\Api\Data\EstimateAddressInterface');
        $estimatedAddress->expects($this->once())->method('setCountryId');
        $estimatedAddress->expects($this->once())->method('setPostcode');
        $estimatedAddress->expects($this->once())->method('setRegion');
        $estimatedAddress->expects($this->once())->method('setRegionId');

        $this->addressFactoryMock->expects($this->once())->method('create')->willReturn($estimatedAddress);

        $this->methodManagementMock->expects($this->once())
            ->method('estimateByAddress')
            ->with($cartId, $estimatedAddress);

        $this->model->estimateByRegistryId($cartId, $giftRegistryId);
    }

    /**
     * @covers \Magento\GiftRegistry\Model\ShippingMethodManagement::estimateByRegistryId
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Unknown gift registry identifier
     */
    public function testEstimateByRegistryIdThrowsExceptionIfGiftRegistryIdIsNotValid()
    {
        $cartId = 1;
        $giftRegistryId = 1;

        $giftRegistry = $this->getMock('\Magento\GiftRegistry\Model\Entity', [], [], '', false);
        $giftRegistry->expects($this->once())->method('loadByEntityItem')->with($giftRegistryId);
        $this->entityFactoryMock->expects($this->once())->method('create')->willReturn($giftRegistry);

        $this->model->estimateByRegistryId($cartId, $giftRegistryId);
    }
}
