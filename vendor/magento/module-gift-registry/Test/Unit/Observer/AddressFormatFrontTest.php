<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Test\Unit\Observer;

class AddressFormatFrontTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Observer\AddressFormatFront
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressFormatMock;

    protected function setUp()
    {
        $this->addressFormatMock = $this->getMock('\Magento\GiftRegistry\Observer\AddressFormat', [], [], '', false);
        $this->model = new \Magento\GiftRegistry\Observer\AddressFormatFront($this->addressFormatMock);
    }

    public function testexecute()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->addressFormatMock->expects($this->once())->method('format')->with($observerMock)->willReturnSelf();
        $this->assertEquals($this->model, $this->model->execute($observerMock));
    }
}
