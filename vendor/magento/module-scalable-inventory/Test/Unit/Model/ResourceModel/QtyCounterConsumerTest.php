<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ScalableInventory\Test\Unit\Model\ResourceModel;

use Magento\CatalogInventory\Model\ResourceModel\Stock;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\ScalableInventory\Api\Counter\ItemsInterface;
use Magento\ScalableInventory\Model\ResourceModel\QtyCounterConsumer;

/**
 * Class QtyCounterConsumerTest
 */
class QtyCounterConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QtyCounterConsumer
     */
    private $consumer;

    /**
     * @var Stock|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stockResource;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->stockResource = $this->getMockBuilder('Magento\CatalogInventory\Model\ResourceModel\Stock')
            ->disableOriginalConstructor()
            ->getMock();

        $this->consumer = $objectManager->getObject(
            'Magento\ScalableInventory\Model\ResourceModel\QtyCounterConsumer',
            ['stockResource' => $this->stockResource]
        );
    }

    public function testProcessMessage()
    {
        $productId = 1;
        $qty = 1;

        $item = $this->getMockBuilder('Magento\ScalableInventory\Api\Counter\ItemInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $item->expects($this->once())
            ->method('getProductId')
            ->willReturn($productId);
        $item->expects($this->once())
            ->method('getQty')
            ->willReturn($qty);

        /** @var ItemsInterface|\PHPUnit_Framework_MockObject_MockObject $qtyObject */
        $qtyObject = $this->getMockBuilder('Magento\ScalableInventory\Api\Counter\ItemsInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $qtyObject->expects($this->once())
            ->method('getItems')
            ->willReturn([$item]);

        $this->consumer->processMessage($qtyObject);
    }
}
