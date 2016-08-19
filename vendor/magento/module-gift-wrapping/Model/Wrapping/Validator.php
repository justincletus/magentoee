<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Model\Wrapping;

use Magento\GiftWrapping\Model\Wrapping;

class Validator extends \Magento\Framework\Validator\AbstractValidator
{
    /**
     * @var array
     */
    protected $requiredFields = [
        'design' => 'Gift Wrapping Design',
        'status' => 'Status',
        'base_price' => 'Price',
    ];

    /**
     * Data validation
     * When data validation fails, getMessages() will provide you array of error messages
     *
     * @param \Magento\GiftWrapping\Model\Wrapping $wrapping
     * @return bool
     */
    public function isValid($wrapping)
    {
        $warnings = [];
        foreach ($this->requiredFields as $code => $label) {
            if (!$wrapping->hasData($code)) {
                $warnings[$code] = 'Field is required: ' . $label;
            }
        }

        $this->_addMessages($warnings);
        return empty($warnings);
    }
}
