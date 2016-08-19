<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\WebsiteRestriction\Test\Unit\Observer;

class AddPrivateSalesLayoutUpdateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\WebsiteRestriction\Model\Observer\AddPrivateSalesLayoutUpdate
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $updateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observer;

    protected function setUp()
    {
        $this->configMock = $this->getMock('Magento\WebsiteRestriction\Model\ConfigInterface');
        $this->updateMock = $this->getMock('Magento\Framework\View\Layout\ProcessorInterface');
        $this->observer = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);

        $layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface');
        $layoutMock->expects($this->any())->method('getUpdate')->will($this->returnValue($this->updateMock));

        $eventMock = $this->getMock('Magento\Framework\Event', ['getLayout'], [], '', false);
        $eventMock->expects($this->any())->method('getLayout')->will($this->returnValue($layoutMock));

        $this->observer->expects($this->any())->method('getEvent')->will($this->returnValue($eventMock));
        $this->model = new \Magento\WebsiteRestriction\Observer\AddPrivateSalesLayoutUpdate($this->configMock);
    }

    public function testExecuteSuccess()
    {
        $this->configMock->expects($this->once())->method('getMode')->will($this->returnValue(1));
        $this->updateMock->expects($this->once())->method('addHandle')->with('restriction_privatesales_mode');
        $this->model->execute($this->observer);
    }

    public function testExecuteWithStrictType()
    {
        $this->configMock->expects($this->once())->method('getMode')->will($this->returnValue('1'));
        $this->updateMock->expects($this->never())->method('addHandle');
        $this->model->execute($this->observer);
    }

    public function testExecuteWithNonAllowedMode()
    {
        $this->configMock->expects($this->once())->method('getMode')->will($this->returnValue('some mode'));
        $this->updateMock->expects($this->never())->method('addHandle');
        $this->model->execute($this->observer);
    }
}
