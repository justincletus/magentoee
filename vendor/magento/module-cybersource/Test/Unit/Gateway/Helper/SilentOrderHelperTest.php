<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Cybersource\Test\Unit\Gateway\Helper;

use Magento\Cybersource\Gateway\Helper\SilentOrderHelper;

class SilentOrderHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testSignFields()
    {
        $key = 'KEY';
        $fieldsToSign = ['field' => 'value'];
        $sign = 'DtfdsHBHfTDNtuphHKwwRlSklaBhY5kyiyaFWVp2AsA=';

        static::assertSame($sign, SilentOrderHelper::signFields($fieldsToSign, $key));
    }
}
