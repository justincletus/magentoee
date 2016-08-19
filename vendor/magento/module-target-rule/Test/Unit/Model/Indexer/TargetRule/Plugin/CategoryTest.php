<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  unit_tests
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Model\Indexer\TargetRule\Plugin;

class CategoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Plugin\Category
     */
    protected $_model;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ruleProductMock;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productRuleMock;

    public function setUp()
    {
        $this->_ruleProductMock = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor',
            [],
            [],
            '',
            false
        );
        $this->_productRuleMock = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor',
            [],
            [],
            '',
            false
        );
        $this->_model = new \Magento\TargetRule\Model\Indexer\TargetRule\Plugin\Category(
            $this->_productRuleMock,
            $this->_ruleProductMock
        );
    }

    public function testCategoryChanges()
    {
        $subjectMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false);

        $subjectMock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue(11));

        $this->_productRuleMock->expects($this->exactly(2))
            ->method('markIndexerAsInvalid');

        $this->_ruleProductMock->expects($this->exactly(2))
            ->method('markIndexerAsInvalid');

        $this->assertEquals(
            $subjectMock,
            $this->_model->afterDelete($subjectMock)
        );

        $this->assertEquals(
            $subjectMock,
            $this->_model->afterSave($subjectMock)
        );
    }
}
