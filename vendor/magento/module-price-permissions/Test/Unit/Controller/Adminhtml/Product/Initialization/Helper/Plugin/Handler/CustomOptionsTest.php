<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PricePermissions\Test\Unit\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

class CustomOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CustomOptions
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    protected function setUp()
    {
        $this->productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $this->model = new \Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler\CustomOptions();
    }

    public function testHandleProductWithoutOptions()
    {
        $this->productMock->expects(
            $this->once()
        )->method(
            'getData'
        )->with(
            'product_options'
        )->will(
            $this->returnValue(null)
        );

        $this->productMock->expects($this->never())->method('setData');

        $this->model->handle($this->productMock);
    }

    public function testHandleProductWithoutOriginalOptions()
    {
        $this->productMock->expects($this->once())->method('getOptions')->will($this->returnValue([]));
        $options = [
            'one' => ['price' => '10', 'price_type' => '20'],
            'two' => ['values' => 123],
            'three' => [
                'values' => [['price' => 30, 'price_type' => 40], ['price' => 50, 'price_type' => 60]],
            ],
        ];

        $expectedData = [
            'one' => ['price' => '0', 'price_type' => '0'],
            'two' => ['values' => 123],
            'three' => [
                'values' => [['price' => 0, 'price_type' => 0], ['price' => 0, 'price_type' => 0]],
            ],
        ];

        $this->productMock->expects(
            $this->once()
        )->method(
            'getData'
        )->with(
            'product_options'
        )->will(
            $this->returnValue($options)
        );

        $this->productMock->expects($this->once())->method('setData')->with('product_options', $expectedData);

        $this->model->handle($this->productMock);
    }

    public function testHandleProductWithOriginalOptions()
    {
        $mockedMethodList = [
            'getOptionId',
            '__wakeup',
            'getType',
            'getPriceType',
            'getGroupByType',
            'getPrice',
            'getValues',
        ];

        $optionOne = $this->getMock('\Magento\Catalog\Model\Product\Option', $mockedMethodList, [], '', false);
        $optionTwo = $this->getMock('\Magento\Catalog\Model\Product\Option', $mockedMethodList, [], '', false);
        $optionTwoValue = $this->getMock(
            '\Magento\Catalog\Model\Product\Option\Value',
            ['getOptionTypeId', 'getPriceType', 'getPrice', '__wakeup'],
            [],
            '',
            false
        );

        $optionOne->expects($this->any())->method('getOptionId')->will($this->returnValue('one'));
        $optionOne->expects($this->any())->method('getType')->will($this->returnValue(2));
        $optionOne->expects(
            $this->any()
        )->method(
            'getGroupByType'
        )->will(
            $this->returnValue(\Magento\Catalog\Model\Product\Option::OPTION_GROUP_DATE)
        );
        $optionOne->expects($this->any())->method('getPrice')->will($this->returnValue(10));
        $optionOne->expects($this->any())->method('getPriceType')->will($this->returnValue(2));

        $optionTwo->expects($this->any())->method('getOptionId')->will($this->returnValue('three'));
        $optionTwo->expects($this->any())->method('getType')->will($this->returnValue(3));
        $optionTwo->expects(
            $this->any()
        )->method(
            'getGroupByType'
        )->will(
            $this->returnValue(\Magento\Catalog\Model\Product\Option::OPTION_GROUP_SELECT)
        );
        $optionTwo->expects($this->any())->method('getValues')->will($this->returnValue([$optionTwoValue]));

        $optionTwoValue->expects($this->any())->method('getOptionTypeId')->will($this->returnValue(1));
        $optionTwoValue->expects($this->any())->method('getPrice')->will($this->returnValue(100));
        $optionTwoValue->expects($this->any())->method('getPriceType')->will($this->returnValue(2));

        $this->productMock->expects(
            $this->once()
        )->method(
            'getOptions'
        )->will(
            $this->returnValue([$optionOne, $optionTwo])
        );

        $options = [
            'one' => ['price' => '10', 'price_type' => '20', 'type' => 2],
            'two' => ['values' => 123, 'type' => 10],
            'three' => [
                'type' => 3,
                'values' => [['price' => 30, 'price_type' => 40, 'option_type_id' => '1']],
            ],
        ];

        $expectedData = [
            'one' => ['price' => 10, 'price_type' => 2, 'type' => 2],
            'two' => ['values' => 123, 'type' => 10],
            'three' => [
                'type' => 3,
                'values' => [['price' => 100, 'price_type' => 2, 'option_type_id' => 1]],
            ],
        ];

        $this->productMock->expects(
            $this->once()
        )->method(
            'getData'
        )->with(
            'product_options'
        )->will(
            $this->returnValue($options)
        );

        $this->productMock->expects($this->once())->method('setData')->with('product_options', $expectedData);

        $this->model->handle($this->productMock);
    }
}
