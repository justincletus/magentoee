<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  unit_tests
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Model\Indexer\TargetRule\Product;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule
     */
    protected $_ruleIndexer;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ruleProductProcessor;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productRuleProcessor;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Action\Full|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_actionFull;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Action\Clean|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_actionClean;

    /**
     * @var Rule\Action\CleanDeleteProduct|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_actionCleanDeleteProduct;

    public function setUp()
    {
        $this->_ruleProductProcessor = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor',
            [],
            [],
            '',
            false
        );
        $this->_productRuleProcessor = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor',
            [],
            [],
            '',
            false
        );
        $this->_actionFull = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Action\Full',
            [],
            [],
            '',
            false
        );
        $actionRow = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\Row',
            [],
            [],
            '',
            false
        );
        $actionRows = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\Rows',
            [],
            [],
            '',
            false
        );
        $this->_actionClean = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Action\Clean',
            [],
            [],
            '',
            false
        );
        $this->_actionCleanDeleteProduct = $this->getMock(
            'Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\CleanDeleteProduct',
            [],
            [],
            '',
            false
        );
        $this->_ruleIndexer = new \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule(
            $actionRow,
            $actionRows,
            $this->_actionFull,
            $this->_ruleProductProcessor,
            $this->_productRuleProcessor,
            $this->_actionClean,
            $this->_actionCleanDeleteProduct
        );
    }

    public function testFullReindexIfNotExecutedRelatedIndexer()
    {
        $this->_actionFull->expects($this->once())
            ->method('execute');
        $this->_ruleProductProcessor->expects($this->once())
            ->method('isFullReindexPassed')
            ->will($this->returnValue(false));
        $this->_ruleProductProcessor->expects($this->once())
            ->method('setFullReindexPassed');
        $this->_ruleIndexer->executeFull();
    }

    public function testFullReindexIfRelatedIndexerPassed()
    {
        $this->_actionFull->expects($this->never())
            ->method('execute');
        $this->_ruleProductProcessor->expects($this->once())
            ->method('isFullReindexPassed')
            ->will($this->returnValue(true));
        $this->_ruleProductProcessor->expects($this->never())
            ->method('setFullReindexPassed');
        $this->_ruleIndexer->executeFull();
    }
}
