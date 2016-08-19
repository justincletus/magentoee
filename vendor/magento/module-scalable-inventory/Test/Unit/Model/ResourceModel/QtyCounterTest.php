<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ScalableInventory\Test\Unit\Model\ResourceModel;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class QtyCounterTest
 */
class QtyCounterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ScalableInventory\Model\Counter\ItemsBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $itemsBuilder;

    /**
     * @var \Magento\Framework\MessageQueue\PublisherProxy|\PHPUnit_Framework_MockObject_MockObject
     */
    private $publisher;

    /**
     * @var \Magento\ScalableInventory\Model\ResourceModel\QtyCounter
     */
    private $resource;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->itemsBuilder = $this->getMockBuilder('Magento\ScalableInventory\Model\Counter\ItemsBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->publisher = $this->getMockBuilder('Magento\Framework\MessageQueue\PublisherProxy')
            ->disableOriginalConstructor()
            ->getMock();

        $this->resource = $objectManager->getObject(
            'Magento\ScalableInventory\Model\ResourceModel\QtyCounter',
            ['itemsBuilder' => $this->itemsBuilder, 'publisher' => $this->publisher]
        );
    }

    public function testCorrectItemsQty()
    {
        $items = [4 => 2, 23 => 12];
        $websiteId = 1;
        $operator = '-';

        $itemsObject = $this->getMockBuilder('Magento\ScalableInventory\Api\Counter\ItemsInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->itemsBuilder->expects($this->once())
            ->method('build')
            ->with($items, $websiteId, $operator)
            ->willReturn($itemsObject);

        $this->publisher->expects($this->once())
            ->method('publish')
            ->with('inventory.counter.updated', $itemsObject);

        $this->resource->correctItemsQty($items, $websiteId, $operator);
    }
}
