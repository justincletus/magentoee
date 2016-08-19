<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Test\Unit\Observer;

use Magento\Framework\App\Area;

class AddressFormatAdminTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Observer\AddressFormatAdmin
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressFormatMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $designMock;

    protected function setUp()
    {
        $this->addressFormatMock = $this->getMock('\Magento\GiftRegistry\Observer\AddressFormat', [], [], '', false);
        $this->designMock = $this->getMock('\Magento\Framework\View\DesignInterface');
        $this->model = new \Magento\GiftRegistry\Observer\AddressFormatAdmin(
            $this->addressFormatMock,
            $this->designMock
        );
    }

    public function testexecute()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->designMock->expects($this->once())->method('getArea')->willReturn(Area::AREA_FRONTEND);
        $this->addressFormatMock->expects($this->once())->method('format')->with($observerMock)->willReturnSelf();
        $this->assertEquals($this->model, $this->model->execute($observerMock));
    }
}
