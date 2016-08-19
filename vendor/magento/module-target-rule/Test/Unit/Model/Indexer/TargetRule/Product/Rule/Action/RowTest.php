<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  unit_tests
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Model\Indexer\TargetRule\Product\Rule\Action;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class RowTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\Row
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->_model = $objectManager->getObject(
            'Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\Row'
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage We can't rebuild the index for an undefined product.
     */
    public function testEmptyId()
    {
        $this->_model->execute(null);
    }
}
