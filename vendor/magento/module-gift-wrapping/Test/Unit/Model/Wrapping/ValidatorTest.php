<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftWrapping\Test\Unit\Model\Wrapping;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GiftWrapping\Model\Wrapping\Validator */
    protected $validator;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->validator = $this->objectManagerHelper->getObject('Magento\GiftWrapping\Model\Wrapping\Validator');
    }

    public function testValidateWithError()
    {
        $wrapping = $this->objectManagerHelper->getObject('Magento\GiftWrapping\Model\Wrapping');
        $wrapping->setData('status', 'Status');
        $wrapping->setData('base_price', 'Price');

        $this->assertFalse($this->validator->isValid($wrapping));
    }

    public function testValidateSuccess()
    {
        $wrapping = $this->objectManagerHelper->getObject('Magento\GiftWrapping\Model\Wrapping');
        $wrapping->setData('status', 'Status');
        $wrapping->setData('base_price', 'Price');
        $wrapping->setData('design', 'Design');

        $this->assertTrue($this->validator->isValid($wrapping));
    }
}
