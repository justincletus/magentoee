<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\GiftRegistry\Test\Unit\Block\Customer\Edit;

class AbstractEditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\GiftRegistry\Block\Customer\Edit\AbstractEdit
     */
    protected $block;

    protected function setUp()
    {
        $this->contextMock = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $this->localeDateMock = $this->getMock('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $this->layoutMock = $this->getMock('\Magento\Framework\View\LayoutInterface');
        $this->contextMock->expects($this->any())->method('getLayout')->will($this->returnValue($this->layoutMock));
        $this->contextMock
            ->expects($this->any())
            ->method('getLocaleDate')
            ->will($this->returnValue($this->localeDateMock));
        $requestMock = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()->getMock();
        $this->contextMock->expects($this->any())->method('getRequest')->will($this->returnValue($requestMock));
        $assertRepoMock = $this->getMock('\Magento\Framework\View\Asset\Repository', [], [], '', false);
        $this->contextMock
            ->expects($this->once())
            ->method('getAssetRepository')
            ->will($this->returnValue($assertRepoMock));

        $assertRepoMock->expects($this->once())->method('getUrlWithParams');
        $this->block = $this->getMockForAbstractClass('Magento\GiftRegistry\Block\Customer\Edit\AbstractEdit',
            [
                $this->contextMock,
                $this->getMock('Magento\Directory\Helper\Data', [], [], '', false),
                $this->getMock('Magento\Framework\Json\EncoderInterface'),
                $this->getMock('Magento\Framework\App\Cache\Type\Config', [], [], '', false),
                $this->getMock('Magento\Directory\Model\ResourceModel\Region\CollectionFactory', ['create'], [], '', false),
                $this->getMock('Magento\Directory\Model\ResourceModel\Country\CollectionFactory', ['create'], [], '', false),
                $this->getMock('Magento\Framework\Registry', [], [], '', false),
                $this->getMock('Magento\Customer\Model\Session', [], [], '', false),
                $this->getMock('Magento\GiftRegistry\Model\Attribute\Config', [], [], '', false),
                []
            ]
        );
    }

    public function testGetCalendarDateHtml()
    {
        $value = '07/24/14';
        $dateTime = new \DateTime($value);
        $methods = ['setId', 'setName', 'setValue', 'setClass', 'setImage', 'setDateFormat', 'getHtml'];
        $block = $this->getMock('Magento\GiftRegistry\Block\Customer\Date', $methods, [], '', false);
        $this->localeDateMock
            ->expects($this->once())
            ->method('formatDateTime')
            ->with($dateTime, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE)
            ->will($this->returnValue($value));
        $this->localeDateMock
            ->expects($this->once())
            ->method('getDateFormat')
            ->with(\IntlDateFormatter::MEDIUM)
            ->will($this->returnValue('format'));
        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with('Magento\GiftRegistry\Block\Customer\Date')->will($this->returnValue($block));
        $block->expects($this->once())->method('setId')->with('id')->will($this->returnSelf());
        $block->expects($this->once())->method('setName')->with('name')->will($this->returnSelf());
        $block->expects($this->once())->method('setValue')->with($value)->will($this->returnSelf());
        $block->expects($this->once())
            ->method('setClass')
            ->with(' product-custom-option datetime-picker input-text validate-date')
            ->will($this->returnSelf());
        $block->expects($this->once())
            ->method('setImage')
            ->will($this->returnSelf());
        $block->expects($this->once())
            ->method('setDateFormat')
            ->with('format')
            ->will($this->returnSelf());
        $block->expects($this->once())->method('getHtml')->will($this->returnValue('expected_html'));
        $this->assertEquals('expected_html', $this->block->getCalendarDateHtml('name', 'id', $value));
    }
}
