<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Worldpay\Gateway\Response;

class CvvHandler extends AvsHandler
{
    /**
     * @var array
     */
    static protected $codesPosition = [
        0 => 'cvv_result'
    ];

    /**
     * @var string
     */
    const FRAUD_CASE = 'cvv_fraud_case';
}
