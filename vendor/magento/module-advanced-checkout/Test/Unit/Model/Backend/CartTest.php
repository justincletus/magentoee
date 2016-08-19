<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCheckout\Test\Unit\Model\Backend;

/**
 * Test class for \Magento\AdvancedCheckout\Model\Backend\Cart
 */
class CartTest extends \PHPUnit_Framework_TestCase
{
    public function testGetActualQuote()
    {
        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $quote = $this->getMock('Magento\Quote\Model\Quote', ['getQuote', '__wakeup'], [], '', false);
        $quote->expects($this->once())->method('getQuote')->will($this->returnValue('some value'));
        /** @var Cart $model */
        $model = $helper->getObject('Magento\AdvancedCheckout\Model\Backend\Cart');
        $model->setQuote($quote);
        $this->assertEquals('some value', $model->getActualQuote());
    }
}
