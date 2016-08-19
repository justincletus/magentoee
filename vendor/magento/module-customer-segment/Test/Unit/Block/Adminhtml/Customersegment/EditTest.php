<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\CustomerSegment\Test\Unit\Block\Adminhtml\Customersegment;


class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Edit
     */
    protected $model;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\CustomerSegment\Model\Segment
     */
    protected $segment;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Backend\Block\Widget\Button\ButtonList
     */
    protected $buttonList;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    protected function setUp()
    {
        $this->segment = $this->getMock(
            'Magento\CustomerSegment\Model\Segment', ['getId', 'getSegmentId', 'getName', '__wakeup'], [], '', false
        );

        $this->registry = $this->getMock('Magento\Framework\Registry', [], [], '', false);
        $this->registry
            ->expects($this->any())
            ->method('registry')
            ->with($this->equalTo('current_customer_segment'))
            ->will($this->returnValue($this->segment));

        $this->urlBuilder = $this->getMockForAbstractClass('Magento\Framework\UrlInterface', [], '', false);

        $this->buttonList = $this->getMock('Magento\Backend\Block\Widget\Button\ButtonList', [], [], '', false);
        $this->buttonList
            ->expects($this->any())
            ->method('add')
            ->will($this->returnSelf());

        $this->request = $this->getMock('Magento\Framework\App\RequestInterface', [], [], '', false);
        $this->escaper = $this->getMock('Magento\Framework\Escaper', [], [], '', false);

        $this->context = $this->getMockBuilder('Magento\Backend\Block\Widget\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->context
            ->expects($this->once())
            ->method('getButtonList')
            ->will($this->returnValue($this->buttonList));
        $this->context
            ->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($this->urlBuilder));
        $this->context
            ->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $this->context
            ->expects($this->once())
            ->method('getEscaper')
            ->will($this->returnValue($this->escaper));

        $this->model = new \Magento\CustomerSegment\Block\Adminhtml\Customersegment\Edit(
            $this->context,
            $this->registry
        );
    }

    protected function tearDown()
    {
        unset(
            $this->model,
            $this->segment,
            $this->registry,
            $this->urlBuilder,
            $this->buttonList,
            $this->request,
            $this->escaper,
            $this->context
        );
    }

    public function testGetMatchUrl()
    {
        $this->segment
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->urlBuilder
            ->expects($this->any())
            ->method('getUrl')
            ->with('*/*/match', ['id' => $this->segment->getId()])
            ->will($this->returnValue('http://some_url'));

        $this->assertContains('http://some_url', (string)$this->model->getMatchUrl());
    }

    public function testGetHeaderText()
    {
        $this->segment
            ->expects($this->once())
            ->method('getSegmentId')
            ->will($this->returnValue(false));

        $this->assertEquals('New Segment', $this->model->getHeaderText());
    }

    public function testGetHeaderTextWithSegmentId()
    {
        $segmentName = 'test_segment_name';

        $this->segment
            ->expects($this->once())
            ->method('getSegmentId')
            ->will($this->returnValue(1));
        $this->segment
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($segmentName));

        $this->escaper
            ->expects($this->once())
            ->method('escapeHtml')
            ->will($this->returnValue($segmentName));

        $this->assertEquals(sprintf("Edit Segment '%s'", $segmentName), $this->model->getHeaderText());
    }
}
