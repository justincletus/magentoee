<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Block\Catalog\Product\ProductList;

class RelatedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Block\Catalog\Product\ProductList\Related
     */
    protected $block;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->registry = $this->getMock('Magento\Framework\Registry', ['registry'], [], '', false);
        $this->cart = $this->getMock('Magento\Checkout\Model\Cart', ['getProductIds'], [], '', false);
        $this->block = $objectManager->getObject(
            'Magento\TargetRule\Block\Catalog\Product\ProductList\Related',
            [
                'cart' => $this->cart,
                'registry' => $this->registry
            ]
        );
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    /**
     * test for getExcludeProductIds
     */
    public function testGetExcludeProductIds()
    {
        $productMock = $this->getMock('Magento\Catalog\Model\Product', ['getEntityId', '__wakeup'], [], '', false);
        $this->registry->expects($this->once())
            ->method('registry')
            ->will($this->returnValue($productMock));
        $this->cart->expects($this->once())
            ->method('getProductIds')
            ->will($this->returnValue(['1', '2', '4']));
        $productMock->expects($this->once())
            ->method('getEntityId')
            ->will($this->returnValue('6'));

        $this->assertEquals([1, 2, 4, 6], $this->block->getExcludeProductIds());
    }


    /**
     * @dataProvider canItemsAddToCartDataProvider
     * @param bool $isComposite
     * @param bool $isSaleable
     * @param bool $hasRequiredOptions
     * @param bool $canItemsAddToCart
     */
    public function testCanItemsAddToCart($isComposite, $isSaleable, $hasRequiredOptions, $canItemsAddToCart)
    {
        $product = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['isComposite', 'isSaleable', 'getRequiredOptions'],
            [],
            '',
            false
        );
        $product->expects($this->any())->method('isComposite')->willReturn($isComposite);
        $product->expects($this->any())->method('isSaleable')->willReturn($isSaleable);
        $product->expects($this->any())->method('getRequiredOptions')->willReturn($hasRequiredOptions);

        $itemsCollection = new \ReflectionProperty(
            'Magento\TargetRule\Block\Catalog\Product\ProductList\Related',
            '_items'
        );
        $itemsCollection->setAccessible(true);
        $itemsCollection->setValue($this->block, [$product]);

        $this->assertEquals(
            $canItemsAddToCart,
            $this->block->canItemsAddToCart()
        );
    }

    public function canItemsAddToCartDataProvider()
    {
        return [
            [false, true, false, true],
            [false, false, false, false],
            [true, false, false, false],
            [true, false, true, false],
        ];
    }
}
