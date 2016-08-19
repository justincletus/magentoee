<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Test\Unit\Block\Adminhtml\Sales\Order\View;

class LinkTest extends \PHPUnit_Framework_TestCase
{
    public function testCanDisplayGiftWrappingForItem()
    {
        $giftWrappingData = $this->getMock(
            'Magento\GiftWrapping\Helper\Data',
            ['isGiftWrappingAvailableForItems'],
            [],
            '',
            false
        );
        $giftWrappingData->expects($this->once())
            ->method('isGiftWrappingAvailableForItems')
            ->with($this->equalTo(1))
            ->will($this->returnValue(true));

        $typeInstance = $this->getMock('Magento\Catalog\Model\Product\Type\Simple', [], [], '', false);

        $product = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getTypeInstance', 'getGiftWrappingAvailable', '__wakeup'],
            [],
            '',
            false
        );
        $product->expects($this->once())->method('getTypeInstance')->will($this->returnValue($typeInstance));
        $product->expects($this->once())->method('getGiftWrappingAvailable')->will($this->returnValue(null));

        $orderItem = $this->getMock(
            'Magento\Quote\Model\Quote\Item',
            ['getProduct', 'getStoreId', '__wakeup'],
            [],
            '',
            false
        );
        $orderItem->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $orderItem->expects($this->once())->method('getStoreId')->will($this->returnValue(1));

        $block1 = $this->getMock(
            'Magento\GiftMessage\Block\Adminhtml\Sales\Order\Create\Giftoptions',
            ['getItem'],
            [],
            '',
            false
        );
        $block1->expects($this->any())->method('getItem')->will($this->returnValue($orderItem));

        $layout = $this->getMock(
            'Magento\Framework\View\Layout',
            ['getParentName', 'getBlock'],
            [],
            '',
            false
        );
        $layout->expects($this->any())
            ->method('getParentName')
            ->with($this->equalTo('nameInLayout'))
            ->will($this->returnValue('parentName'));
        $layout->expects($this->any())
            ->method('getBlock')
            ->with($this->equalTo('parentName'))
            ->will($this->returnValue($block1));

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $context = $objectManager->getObject('Magento\Backend\Block\Template\Context', ['layout' => $layout]);

        /** @var \Magento\GiftWrapping\Block\Adminhtml\Sales\Order\Create\Link $websiteModel */
        $block = $objectManager->getObject(
            'Magento\GiftWrapping\Block\Adminhtml\Sales\Order\Create\Link',
            ['context' => $context, 'giftWrappingData' => $giftWrappingData]
        );
        $block->setNameInLayout('nameInLayout');

        $this->assertTrue($block->canDisplayGiftWrappingForItem());
    }
}
