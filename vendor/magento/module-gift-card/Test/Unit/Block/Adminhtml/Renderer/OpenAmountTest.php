<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCard\Test\Unit\Block\Adminhtml\Renderer;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class OpenAmountTest
 */
class OpenAmountTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCard\Block\Adminhtml\Renderer\OpenAmount
     */
    protected $block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $factory;

    /**
     * @var \Magento\Framework\Data\Form\Element\Checkbox
     */
    protected $element;

    public function setUp()
    {
        $this->factory = $this->getMockBuilder('\Magento\Framework\Data\Form\Element\Factory')
            ->disableOriginalConstructor()->setMethods(['create'])->getMock();
        $objectManager = new ObjectManager($this);
        $this->element = $objectManager->getObject('Magento\Framework\Data\Form\Element\Checkbox');
        $form = $this->getMockBuilder('Magento\Framework\Data\Form')->disableOriginalConstructor()
            ->setMethods(['getHtmlIdPrefix', 'getHtmlIdSuffix'])
            ->getMock();
        $form->expects($this->any())->method('getHtmlIdPrefix')->willReturn('');
        $form->expects($this->any())->method('getHtmlIdSuffix')->willReturn('');

        $this->factory->expects($this->once())->method('create')->willReturn($this->element);
        $this->block = $objectManager->getObject(
            'Magento\GiftCard\Block\Adminhtml\Renderer\OpenAmount',
            [
                'factoryElement' => $this->factory
            ]
        );
        $this->block->setForm($form);
    }

    public function testGetElementHtml()
    {
        $this->block->setReadonlyDisabled(true);
        $this->assertContains('disabled', $this->block->getElementHtml());
    }
}
