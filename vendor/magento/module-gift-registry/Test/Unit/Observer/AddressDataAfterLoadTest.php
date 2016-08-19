<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Test\Unit\Observer;

class AddressDataAfterLoadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Observer\AddressDataAfterLoad
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $giftRegistryDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityFactoryMock;

    protected function setUp()
    {
        $this->giftRegistryDataMock = $this->getMock('\Magento\GiftRegistry\Helper\Data', [], [], '', false);
        $this->customerSessionMock = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        $this->entityFactoryMock = $this->getMock(
            '\Magento\GiftRegistry\Model\EntityFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->model = new \Magento\GiftRegistry\Observer\AddressDataAfterLoad(
            $this->giftRegistryDataMock,
            $this->customerSessionMock,
            $this->entityFactoryMock
        );
    }

    public function testexecuteIfGiftRegistryEntityIdIsNull()
    {
        $registryItemId = 100;
        $eventMock = $this->getMock('\Magento\Framework\Event', ['getDataObject'], [], '', false);
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $observerMock->expects($this->once())->method('getEvent')->willReturn($eventMock);

        $dataObjectMock = $this->getMock('\Magento\Framework\DataObject', ['getGiftregistryItemId'], [], '', false);
        $eventMock->expects($this->once())->method('getDataObject')->willReturn($dataObjectMock);

        $dataObjectMock->expects($this->once())->method('getGiftregistryItemId')->willReturn($registryItemId);

        $entityMock = $this->getMock(
            '\Magento\GiftRegistry\Model\Entity',
            ['loadByEntityItem', 'getId'],
            [],
            '',
            false
        );
        $this->entityFactoryMock->expects($this->once())->method('create')->willReturn($entityMock);

        $entityMock->expects($this->once())->method('loadByEntityItem')->with($registryItemId)->willReturnSelf();
        $entityMock->expects($this->once())->method('getId')->willReturn(null);

        $this->assertEquals($this->model, $this->model->execute($observerMock));
    }

    public function testexecute()
    {
        $prefix = 'prefix';
        $registryItemId = 100;
        $entityId = 200;
        $customerId = 300;
        $addressData = ['data' => 'value'];

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getDataObject'], [], '', false);
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $observerMock->expects($this->once())->method('getEvent')->willReturn($eventMock);

        $dataObjectMock = $this->getMock(
            '\Magento\Framework\DataObject',
            ['getGiftregistryItemId', 'setId', 'setCustomerId', 'addData'],
            [],
            '',
            false
        );
        $eventMock->expects($this->once())->method('getDataObject')->willReturn($dataObjectMock);

        $dataObjectMock->expects($this->once())->method('getGiftregistryItemId')->willReturn($registryItemId);

        $entityMock = $this->getMock(
            '\Magento\GiftRegistry\Model\Entity',
            ['loadByEntityItem', 'getId', 'exportAddress'],
            [],
            '',
            false
        );
        $this->entityFactoryMock->expects($this->once())->method('create')->willReturn($entityMock);

        $entityMock->expects($this->once())->method('loadByEntityItem')->with($registryItemId)->willReturnSelf();
        $entityMock->expects($this->once())->method('getId')->willReturn($entityId);

        $customerMock = $this->getMock('\Magento\Customer\Model\Customer', [], [], '', false);
        $customerMock->expects($this->once())->method('getId')->willReturn($customerId);
        $this->giftRegistryDataMock->expects($this->once())->method('getAddressIdPrefix')->willReturn($prefix);
        $this->customerSessionMock->expects($this->once())->method('getCustomer')->willReturn($customerMock);

        $exportedAddressMock = $this->getMock('\Magento\Customer\Model\Address', [], [], '', false);
        $exportedAddressMock->expects($this->once())->method('getData')->willReturn($addressData);
        $entityMock->expects($this->once())->method('exportAddress')->willReturn($exportedAddressMock);

        $dataObjectMock->expects($this->once())->method('setId')->with($prefix . $registryItemId)->willReturnSelf();
        $dataObjectMock->expects($this->once())->method('setCustomerId')->with($customerId)->willReturnSelf();
        $dataObjectMock->expects($this->once())->method('addData')->with($addressData)->willReturnSelf();

        $this->assertEquals($this->model, $this->model->execute($observerMock));
    }
}
