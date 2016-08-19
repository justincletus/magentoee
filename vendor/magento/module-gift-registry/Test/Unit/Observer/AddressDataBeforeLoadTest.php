<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Test\Unit\Observer;

class AddressDataBeforeLoadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Observer\AddressDataBeforeLoad
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $giftRegistryDataMock;

    protected function setUp()
    {
        $this->giftRegistryDataMock = $this->getMock('\Magento\GiftRegistry\Helper\Data', [], [], '', false);
        $this->model = new \Magento\GiftRegistry\Observer\AddressDataBeforeLoad($this->giftRegistryDataMock);
    }

    public function testexecute()
    {
        $addressId = 'prefixId';
        $prefix = 'prefix';
        $dataObject = $this->getMock(
            '\Magento\Framework\DataObject',
            ['setGiftregistryItemId', 'setCustomerAddressId'],
            [],
            '',
            false
        );
        $dataObject->expects($this->once())->method('setGiftregistryItemId')->with('Id')->willReturnSelf();
        $dataObject->expects($this->once())->method('setCustomerAddressId')->with($addressId)->willReturnSelf();

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getValue', 'getDataObject'], [], '', false);
        $eventMock->expects($this->once())->method('getValue')->willReturn($addressId);
        $eventMock->expects($this->once())->method('getDataObject')->willReturn($dataObject);

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $observerMock->expects($this->exactly(2))->method('getEvent')->willReturn($eventMock);

        $this->giftRegistryDataMock->expects($this->once())->method('getAddressIdPrefix')->willReturn($prefix);

        $this->assertEquals($this->model, $this->model->execute($observerMock));
    }
}
