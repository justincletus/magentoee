<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  unit_tests
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Model\Indexer\TargetRule\Plugin;

class ImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Plugin\Import
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
        $this->_model = new \Magento\TargetRule\Model\Indexer\TargetRule\Plugin\Import(
            $this->_productRuleMock,
            $this->_ruleProductMock
        );
    }

    public function testAfterImportSource()
    {
        $subjectMock = $this->getMock('Magento\ImportExport\Model\Import', [], [], '', false);
        $result = 'result';
        $this->_productRuleMock->expects($this->once())
            ->method('markIndexerAsInvalid');

        $this->_ruleProductMock->expects($this->once())
            ->method('markIndexerAsInvalid');

        $this->assertEquals(
            $result,
            $this->_model->afterImportSource($subjectMock, $result)
        );
    }
}
