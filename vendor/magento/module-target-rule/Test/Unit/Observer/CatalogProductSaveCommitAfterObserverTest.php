<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Observer;

use Magento\Catalog\Model\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class CatalogProductSaveCommitAfterObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tested observer
     *
     * @var \Magento\TargetRule\Observer\CatalogProductSaveCommitAfterObserver
     */
    protected $_observer;

    /**
     * Product-Rule processor mock
     *
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productRuleProcessorMock;

    public function setUp()
    {
        $this->_productRuleProcessorMock = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor',
            [],
            [],
            '',
            false
        );

        $this->_observer = (new ObjectManager($this))->getObject(
            'Magento\TargetRule\Observer\CatalogProductSaveCommitAfterObserver',
            [
                'productRuleIndexerProcessor' => $this->_productRuleProcessorMock,
            ]
        );
    }

    public function testCatalogProductSaveCommitAfter()
    {
        $productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            ['getId', '__sleep', '__wakeup'],
            [],
            '',
            false
        );
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent'], [], '', false);
        $eventMock = $this->getMock('\Magento\Framework\Event', ['getProduct'], [], '', false);
        $eventMock->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($productMock));

        $observerMock->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($eventMock));

        $productMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->_productRuleProcessorMock->expects($this->once())
            ->method('reindexRow')
            ->with(1);

        $this->_observer->execute($observerMock);
    }
}
