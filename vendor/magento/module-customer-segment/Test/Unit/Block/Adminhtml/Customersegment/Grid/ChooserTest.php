<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerSegment\Test\Unit\Block\Adminhtml\Customersegment\Grid;

use Magento\Framework\App\Filesystem\DirectoryList;

class ChooserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Chooser
     */
    protected $model;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $store;

    /**
     * @var \Magento\CustomerSegment\Model\SegmentFactory
     */
    protected $segmentFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    protected function setUp()
    {
        $this->helper = $this->getMock('Magento\Backend\Helper\Data', [], [], '', false);
        $this->store = $this->getMock('Magento\Store\Model\System\Store', [], [], '', false);
        $this->segmentFactory = $this->getMock(
            'Magento\CustomerSegment\Model\SegmentFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->request = $this->getMock('Magento\Framework\App\RequestInterface', [], [], '', false);

        $writeInterface = $this->getMock('Magento\Framework\Filesystem\Directory\WriteInterface');
        $this->filesystem = $this->getMock('Magento\Framework\Filesystem', [], [], '', false);
        $this->filesystem
            ->expects($this->once())
            ->method('getDirectoryWrite')
            ->with($this->equalTo(DirectoryList::VAR_DIR))
            ->will($this->returnValue($writeInterface));

        $this->urlBuilder = $this->getMockForAbstractClass('Magento\Framework\UrlInterface', [], '', false);

        $this->context = $this->getMockBuilder('Magento\Backend\Block\Widget\Context')
            ->disableOriginalConstructor()
            ->getMock();

        $this->context
            ->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($this->urlBuilder));
        $this->context
            ->expects($this->once())
            ->method('getFilesystem')
            ->will($this->returnValue($this->filesystem));
        $this->context
            ->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->request));

        $this->model = new \Magento\CustomerSegment\Block\Adminhtml\Customersegment\Grid\Chooser(
            $this->context,
            $this->helper,
            $this->store,
            $this->segmentFactory
        );
    }

    protected function tearDown()
    {
        unset(
            $this->model,
            $this->helper,
            $this->store,
            $this->segmentFactory,
            $this->request,
            $this->filesystem,
            $this->urlBuilder,
            $this->context
        );
    }

    public function testGetRowClickCallback()
    {
        $data = 'test_row_click_callback';

        $this->model->setData('row_click_callback', $data);

        $this->assertEquals($data, $this->model->getRowClickCallback());
    }

    public function testGetGridUrl()
    {
        $this->urlBuilder
            ->expects($this->any())
            ->method('getUrl')
            ->with('customersegment/index/chooserGrid', ['_current' => true])
            ->willReturn('http://some_url');

        $this->assertContains('http://some_url', (string)$this->model->getGridUrl());
    }
}
