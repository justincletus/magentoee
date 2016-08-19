<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCheckout\Test\Unit\Controller;

class CartTest extends \PHPUnit_Framework_TestCase
{
    public function testControllerImplementsProductViewInterface()
    {
        $this->assertInstanceOf(
            'Magento\Catalog\Controller\Product\View\ViewInterface',
            $this->getMock('Magento\AdvancedCheckout\Controller\Cart', [], [], '', false)
        );
    }
}
