<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Model\Rule\Condition;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class CombineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Combine model
     *
     * @var \Magento\TargetRule\Model\Rule\Condition\Combine
     */
    protected $_combine;

    /**
     * Return array
     *
     * @var array
     */
    protected $returnArray = [
        'value' => 'Test',
        'label' => 'Test Conditions',
    ];

    public function setUp()
    {
        $attribute = $this->getMock(
            '\Magento\TargetRule\Model\Rule\Condition\Product\Attribute',
            ['getNewChildSelectOptions'],
            [],
            '',
            false
        );

        $attribute->expects($this->any())
            ->method('getNewChildSelectOptions')
            ->will($this->returnValue($this->returnArray));

        $attributesFactory = $this->getMock(
            '\Magento\TargetRule\Model\Rule\Condition\Product\AttributesFactory',
            ['create'],
            [],
            '',
            false
        );

        $attributesFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($attribute));

        $this->_combine = (new ObjectManager($this))->getObject(
            '\Magento\TargetRule\Model\Rule\Condition\Combine',
            [
                'context' => $this->_getCleanMock('\Magento\Rule\Model\Condition\Context'),
                'attributesFactory' => $attributesFactory,
            ]
        );
    }

    /**
     * Get clean mock by class name
     *
     * @param string $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getCleanMock($className)
    {
        return $this->getMock($className, [], [], '', false);
    }

    public function testGetNewChildSelectOptions()
    {
        $result = [
            '0' => [
                'value' => '',
                'label' => 'Please choose a condition to add.',
            ],
            '1' => [
                'value' => 'Magento\TargetRule\Model\Rule\Condition\Combine',
                'label' => 'Conditions Combination',
            ],
            '2' => $this->returnArray,
        ];

        $this->assertEquals($result, $this->_combine->getNewChildSelectOptions());
    }

    public function testCollectValidatedAttributes()
    {
        $productCollection = $this->_getCleanMock('\Magento\Catalog\Model\ResourceModel\Product\Collection');
        $condition = $this->_getCleanMock('\Magento\TargetRule\Model\Rule\Condition\Combine');

        $condition->expects($this->once())
            ->method('collectValidatedAttributes')
            ->will($this->returnSelf());

        $this->_combine->setConditions([$condition]);

        $this->assertEquals($this->_combine, $this->_combine->collectValidatedAttributes($productCollection));
    }
}
