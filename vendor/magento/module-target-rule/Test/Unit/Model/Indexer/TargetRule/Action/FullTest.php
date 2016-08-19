<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  unit_tests
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Model\Indexer\TargetRule\Action;

class FullTest extends \PHPUnit_Framework_TestCase
{
    public function testFullReindex()
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

        $collectionFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue([1, 2]));

        $resourceMock->expects($this->at(2))
            ->method('saveProductIndex')
            ->will($this->returnValue(1));

        $storeManagerMock = $this->getMockForAbstractClass('\Magento\Store\Model\StoreManagerInterface');
        $timezoneMock = $this->getMockForAbstractClass('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');

        $model = new \Magento\TargetRule\Model\Indexer\TargetRule\Action\Full(
            $ruleFactoryMock,
            $collectionFactoryMock,
            $resourceMock,
            $storeManagerMock,
            $timezoneMock
        );

        $model->execute();
    }
}
