<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogPermissions\Test\Unit\Model\Adminhtml\System\Config\Source;

use Magento\CatalogPermissions\Model\Adminhtml\System\Config\Source\Grant;
use Magento\Framework\Phrase;

/**
 * Unit test for Magento\CatalogPermissions\Model\Adminhtml\System\Config\Source\Grant
 */
class GrantTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogPermissions\Model\Adminhtml\System\Config\Source\Grant
     */
    protected $grant;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->grant = new Grant();
    }

    /**
     * @return void
     */
    public function testToOptionArray()
    {
        foreach ($this->grant->toOptionArray() as $item) {
            $this->assertTrue($item instanceof Phrase || is_string($item));
        }
    }
}
