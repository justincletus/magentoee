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

class CleanDeleteProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\CleanDeleteProduct
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->_model = $objectManager->getObject(
            'Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\CleanDeleteProduct'
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage We can't rebuild the index for an undefined product.
     */
    public function testEmptyIds()
    {
        $this->_model->execute(null);
    }

    public function testCleanDeleteProduct()
    {
        $ruleFactoryMock = $this->getMock(
            'Magento\TargetRule\Model\RuleFactory',
            ['create'],
            [],
            '',
            false
        );

        $collectionFactoryMock = $this->getMock(
            'Magento\TargetRule\Model\ResourceModel\Rule\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $resourceMock = $this->getMock('Magento\TargetRule\Model\ResourceModel\Index', [], [], '', false);

        $resourceMock->expects($this->once())
            ->method('deleteProductFromIndex')
            ->will($this->returnValue(1));

        $storeManagerMock = $this->getMockForAbstractClass('\Magento\Store\Model\StoreManagerInterface');
        $timezoneMock = $this->getMockForAbstractClass('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');

        $model = new \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action\CleanDeleteProduct(
            $ruleFactoryMock,
            $collectionFactoryMock,
            $resourceMock,
            $storeManagerMock,
            $timezoneMock
        );

        $model->execute(2);
    }
}
