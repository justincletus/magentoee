<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  unit_tests
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Model\Indexer\TargetRule\Rule\Product\Action;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class RowsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action\Rows
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->_model = $objectManager->getObject(
            'Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action\Rows'
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Could not rebuild index for empty products array
     */
    public function testEmptyIds()
    {
        $this->_model->execute(null);
    }
}
