<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCard\Test\Unit\Block\Adminhtml\Catalog\Product\Composite\Fieldset;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class GiftcardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCard\Block\Adminhtml\Catalog\Product\Composite\Fieldset\Giftcard
     */
    protected $block;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreRegistry;

    public function setUp()
    {
        $this->coreRegistry = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new ObjectManager($this);
        $this->block = $objectManager->getObject(
            'Magento\GiftCard\Block\Adminhtml\Catalog\Product\Composite\Fieldset\Giftcard',
            [
                'coreRegistry' => $this->coreRegistry
            ]
        );
    }

    public function testGetIsLastFieldsetWithData()
    {
        $this->block->setData('is_last_fieldset', true);

        $this->assertEquals(true, $this->block->getIsLastFieldset());
    }

    public function testGetIsLastFieldset()
    {
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getTypeInstance', 'getOptions', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();
        $typeInstance = $this->getMockBuilder('Magento\GiftCard\Model\Catalog\Product\Type\Giftcard')
            ->setMethods(['getStoreFilter'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->block->setData('product', $product);

        $product->expects($this->once())
            ->method('getTypeInstance')
            ->will($this->returnValue($typeInstance));
        $typeInstance->expects($this->once())
            ->method('getStoreFilter')
            ->with($product)
            ->will($this->returnValue(true));
        $product->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue(null));

        $this->assertEquals(true, $this->block->getIsLastFieldset());
    }
}
