<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Worldpay\Test\Unit\Gateway\Http\Converter;

use Magento\Worldpay\Gateway\Http\Converter\StringToMap;

class StringToMapTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertException()
    {
        $this->setExpectedException(
            '\Magento\Payment\Gateway\Http\ConverterException',
            __('Wrong response type')
        );

        $converter = new StringToMap();
        $converter->convert([]);
    }

    public function testConvertSuccess()
    {
        $converter = new StringToMap();
        static::assertEquals(
            ['Work hard', 'die tired'],
            $converter->convert('Work hard,die tired')
        );
    }
}
