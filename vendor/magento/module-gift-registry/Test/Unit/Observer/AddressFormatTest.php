<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Test\Unit\Observer;

class AddressFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Observer\AddressFormat
     */
    protected $model;

    protected function setUp()
    {
        $this->model = new \Magento\GiftRegistry\Observer\AddressFormat();
    }

    public function testFormatIfGiftRegistryItemIdIsNull()
    {
        $format = 'format';
        $eventMock = $this->getMock('\Magento\Framework\Event', ['getType', 'getAddress'], [], '', false);
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $observerMock->expects($this->exactly(2))->method('getEvent')->willReturn($eventMock);

        $typeMock = $this->getMock(
            '\Magento\Framework\DataObject',
            ['getPrevFormat', 'setDefaultFormat'],
            [],
            '',
            false
        );
        $addressMock = $this->getMock(
            '\Magento\Customer\Model\Address\AbstractAddress',
            ['getGiftregistryItemId'],
            [],
            '',
            false
        );

        $eventMock->expects($this->once())->method('getType')->willReturn($typeMock);
        $eventMock->expects($this->once())->method('getAddress')->willReturn($addressMock);

        $addressMock->expects($this->once())->method('getGiftregistryItemId')->willReturn(null);
        $typeMock->expects($this->exactly(2))->method('getPrevFormat')->willReturn($format);
        $typeMock->expects($this->once())->method('setDefaultFormat')->with($format)->willReturn($format);

        $this->assertEquals($this->model, $this->model->format($observerMock));
    }

    public function testFormat()
    {
        $giftRegistryItemId = 100;
        $format = 'format';
        $eventMock = $this->getMock('\Magento\Framework\Event', ['getType', 'getAddress'], [], '', false);
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $observerMock->expects($this->exactly(2))->method('getEvent')->willReturn($eventMock);

        $typeMock = $this->getMock(
            '\Magento\Framework\DataObject',
            ['getPrevFormat', 'setDefaultFormat', 'getDefaultFormat', 'setPrevFormat'],
            [],
            '',
            false
        );
        $addressMock = $this->getMock(
            '\Magento\Customer\Model\Address\AbstractAddress',
            ['getGiftregistryItemId'],
            [],
            '',
            false
        );

        $eventMock->expects($this->once())->method('getType')->willReturn($typeMock);
        $eventMock->expects($this->once())->method('getAddress')->willReturn($addressMock);

        $addressMock->expects($this->once())->method('getGiftregistryItemId')->willReturn($giftRegistryItemId);

        $typeMock->expects($this->once())->method('getPrevFormat')->willReturn(null);
        $typeMock->expects($this->once())->method('getDefaultFormat')->willReturn($format);
        $typeMock->expects($this->once())->method('setPrevFormat')->with($format)->willReturnSelf();
        $typeMock->expects($this->once())
            ->method('setDefaultFormat')
            ->with(__("Ship to the recipient's address."))
            ->willReturnSelf();

        $this->assertEquals($this->model, $this->model->format($observerMock));
    }
}
