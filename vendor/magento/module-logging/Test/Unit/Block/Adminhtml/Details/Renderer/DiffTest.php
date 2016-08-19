<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Logging\Test\Unit\Block\Adminhtml\Details\Renderer;

/**
 * @magentoAppArea adminhtml
 */
class DiffTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Diff
     */
    protected $_object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_column;

    protected function setUp()
    {
        $escaper = $this->getMock('Magento\Framework\Escaper');
        $escaper->expects($this->any())->method('escapeHtml')->will($this->returnArgument(0));
        $context = $this->getMock('Magento\Backend\Block\Context', [], [], '', false);
        $context->expects($this->once())
            ->method('getEscaper')
            ->will($this->returnValue($escaper));
        $this->_column = $this->getMock(
            'Magento\Backend\Block\Widget\Grid\Column\Extended',
            ['getValues', 'getIndex', 'getHtmlName'],
            [],
            '',
            false
        );

        $this->_object = new \Magento\Logging\Block\Adminhtml\Details\Renderer\Diff($context);
        $this->_object->setColumn($this->_column);
    }

    /**
     * @param array $rowData
     * @param string $expectedResult
     * @dataProvider renderDataProvider
     */
    public function testRender($rowData, $expectedResult)
    {
        $this->_column->expects($this->once())->method('getIndex')->will($this->returnValue('result_data'));
        $this->assertContains($expectedResult, $this->_object->render(new \Magento\Framework\DataObject($rowData)));
    }

    public function renderDataProvider()
    {
        return [
            'allowed' => [
                ['result_data' => 'a:1:{s:5:"allow";a:2:{i:0;s:3:"TMM";i:1;s:3:"USD";}}'],
                '<dd class="value">TMM</dd><dd class="value">USD</dd>',
            ],
            'time' => [
                ['result_data' => 'a:1:{s:4:"time";a:3:{i:0;s:2:"00";i:1;s:2:"00";i:2;s:2:"00";}}'],
                '<dd class="value">00:00:00</dd>',
            ]
        ];
    }
}
