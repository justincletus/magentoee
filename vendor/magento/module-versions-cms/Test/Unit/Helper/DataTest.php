<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldSet;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $elementInFieldSet;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $element;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $container;

    public function setUp()
    {
        $this->fieldSet = $this->getMock('Magento\Framework\Data\Form\Element\Fieldset', [], [], '', false);
        $this->elementInFieldSet = $this->getMock(
            'Magento\Framework\Data\Form\Element\AbstractElement',
            [],
            [],
            '',
            false
        );
        $this->element = $this->getMock('Magento\Framework\Data\Form\Element\AbstractElement', [], [], '', false);
    }

    public function testAddAttributeToFormElements()
    {
        $attributeName = 'test-attribute';
        $attributeValue = 'test-value';

        $this->elementInFieldSet->expects($this->once())->method('setData')->with($attributeName, $attributeValue);

        $this->fieldSet->expects($this->once())->method('getType')->willReturn('fieldset');
        $this->fieldSet->expects($this->once())->method('getElements')->willReturn([$this->elementInFieldSet]);

        $this->element->expects($this->once())->method('setData')->with($attributeName, $attributeValue);

        $this->container = $this->getMock('Magento\Framework\Data\Form\AbstractForm', [], [], '', false);
        $this->container->expects($this->once())->method('getElements')->willReturn([$this->fieldSet, $this->element]);

        /** @var \Magento\VersionsCms\Helper\Data $helper */
        $helper = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))
            ->getObject('Magento\VersionsCms\Helper\Data');
        $helper->addAttributeToFormElements($attributeName, $attributeValue, $this->container);
    }
}
