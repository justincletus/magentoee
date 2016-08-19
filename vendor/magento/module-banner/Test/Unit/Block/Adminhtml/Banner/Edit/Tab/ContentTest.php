<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Test\Unit\Block\Adminhtml\Banner\Edit\Tab;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Content
     */
    protected $content;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Framework\Data\FormFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formFactory;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $wysiwygConfig;

    protected function setUp()
    {
        $this->registry = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->context = $this->getMockBuilder('Magento\Backend\Block\Widget\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->formFactory = $this->getMockBuilder('Magento\Framework\Data\FormFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->wysiwygConfig = $this->getMockBuilder('Magento\Cms\Model\Wysiwyg\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->content = new \Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Content(
            $this->context,
            $this->registry,
            $this->formFactory,
            $this->wysiwygConfig,
            []
        );
    }

    public function testGetTabLabel()
    {
        $this->assertEquals('Content', $this->content->getTabLabel());
    }

    public function testGetTabTitle()
    {
        $this->assertEquals('Content', $this->content->getTabTitle());
    }

    public function testCanShowTab()
    {
        $this->assertTrue($this->content->canShowTab());
    }

    public function testIsHidden()
    {
        $this->assertFalse($this->content->isHidden());
    }
}
