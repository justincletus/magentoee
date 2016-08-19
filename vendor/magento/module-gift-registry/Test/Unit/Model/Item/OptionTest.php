<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Test\Unit\Model\Item;

class OptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param mixed $product
     * @param mixed $expectedProduct
     * @param int $expectedProductId
     * @dataProvider setProductDataProvider
     */
    public function testSetProduct($product, $expectedProduct, $expectedProductId)
    {
        $model = $this->getMock(
            'Magento\GiftRegistry\Model\Item\Option',
            ['getValue', '__wakeup'],
            [],
            '',
            false
        );
        $model->setProduct($product);

        $this->assertEquals($expectedProduct, $model->getProduct());
        $this->assertEquals($expectedProductId, $model->getProductId());
    }

    public function setProductDataProvider()
    {
        $product = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getId', '__sleep', '__wakeup'],
            [],
            '',
            false
        );
        $product->expects($this->any())->method('getId')->will($this->returnValue(3));
        return [[$product, $product, 3], [null, null, null]];
    }
}
