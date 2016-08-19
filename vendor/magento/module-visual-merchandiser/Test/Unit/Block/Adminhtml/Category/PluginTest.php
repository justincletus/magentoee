<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Test\Unit\Block\Adminhtml\Category;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tabs
     */
    protected $tabs;

    /**
     * @var \Magento\VisualMerchandiser\Block\Adminhtml\Category\Plugin
     */
    protected $plugin;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $block;

    /**
     * Set up instances and mock objects
     */
    protected function setUp()
    {
        $this->block = $this->getMock('Magento\Framework\DataObject', ['toHtml'], [], '', false);
        $this->block
            ->expects($this->any())
            ->method('toHtml')
            ->will($this->returnValue('block-html'));

        $this->layout = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);
        $this->layout
            ->expects($this->any())
            ->method('createBlock')
            ->with(
                $this->equalTo('Magento\VisualMerchandiser\Block\Adminhtml\Category\Tab\Merchandiser'),
                $this->equalTo('category.merchandiser.container')
            )
            ->will($this->returnValue($this->block));

        $this->tabs = $this->getMock('Magento\Catalog\Block\Adminhtml\Category\Tabs', [], [], '', false);
        $this->tabs
            ->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($this->layout));


        $this->plugin = (new ObjectManager($this))->getObject(
            'Magento\VisualMerchandiser\Block\Adminhtml\Category\Plugin',
            []
        );
    }

    /**
     * Test plugin method
     */
    public function testBeforeToHtml()
    {
        $this->tabs
            ->expects($this->once())
            ->method('removeTab')
            ->with($this->equalTo('products'));

        $this->tabs
            ->expects($this->once())
            ->method('addTab')
            ->with($this->equalTo('merchandiser'));

        $this->plugin->beforeToHtml($this->tabs);
    }
}
