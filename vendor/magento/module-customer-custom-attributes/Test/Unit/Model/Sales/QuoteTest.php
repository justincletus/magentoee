<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerCustomAttributes\Test\Unit\Model\Sales;

class QuoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerCustomAttributes\Model\Sales\Quote
     */
    protected $quote;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\CustomerCustomAttributes\Model\ResourceModel\Sales\Quote|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    protected function setUp()
    {
        $this->contextMock = $this->getMock('Magento\Framework\Model\Context', [], [], '', false);
        $this->registryMock = $this->getMock('Magento\Framework\Registry');
        $this->resourceMock = $this->getMock(
            'Magento\CustomerCustomAttributes\Model\ResourceModel\Sales\Quote',
            [],
            [],
            '',
            false
        );

        $this->eventManagerMock = $this->getMock('Magento\Framework\Event\ManagerInterface', [], [], '', false);

        $this->contextMock->expects($this->once())
            ->method('getEventDispatcher')
            ->will($this->returnValue($this->eventManagerMock));

        $this->quote = new \Magento\CustomerCustomAttributes\Model\Sales\Quote(
            $this->contextMock,
            $this->registryMock,
            $this->resourceMock
        );
    }

    public function testSaveNewAttribute()
    {
        $attributeMock = $this->getMock('Magento\Customer\Model\Attribute', [], [], '', false);

        $this->resourceMock->expects($this->once())
            ->method('saveNewAttribute')
            ->with($attributeMock);

        $this->assertEquals($this->quote, $this->quote->saveNewAttribute($attributeMock));
    }

    public function testDeleteAttribute()
    {
        $attributeMock = $this->getMock('Magento\Customer\Model\Attribute', [], [], '', false);

        $this->resourceMock->expects($this->once())
            ->method('deleteAttribute')
            ->with($attributeMock);

        $this->assertEquals($this->quote, $this->quote->deleteAttribute($attributeMock));
    }

    public function testAttachAttributeData()
    {
        $salesMock = $this->getMock('Magento\Framework\Model\AbstractModel', [], [], '', false);
        $salesMock->expects($this->once())
            ->method('addData')
            ->with([]);

        $this->assertEquals($this->quote, $this->quote->attachAttributeData($salesMock));
    }

    public function testSaveAttributeData()
    {
        $salesMock = $this->getMock('Magento\Framework\Model\AbstractModel', [], [], '', false);
        $salesMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue([]));
        $salesMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->resourceMock->expects($this->once())
            ->method('save')
            ->with($this->quote)
            ->will($this->returnSelf());

        $this->assertEquals($this->quote, $this->quote->saveAttributeData($salesMock));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testBeforeSaveNegative()
    {
        $salesMock = $this->getMock('Magento\Framework\Model\AbstractModel', [], [], '', false);
        $this->resourceMock->expects($this->once())
            ->method('isEntityExists')
            ->with($this->quote)
            ->will($this->returnValue(false));

        $this->quote->beforeSave();
        $this->assertFalse($this->quote->isSaveAllowed());
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testBeforeSave()
    {
        $salesMock = $this->getMock('Magento\Framework\Model\AbstractModel', [], [], '', false);
        $this->resourceMock->expects($this->once())
            ->method('isEntityExists')
            ->with($this->quote)
            ->will($this->returnValue(true));
        $this->eventManagerMock->expects($this->exactly(2))->method('dispatch');
        $this->quote->beforeSave();
        $this->assertTrue($this->quote->isSaveAllowed());
    }
}
