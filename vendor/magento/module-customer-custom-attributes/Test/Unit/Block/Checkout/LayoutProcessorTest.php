<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerCustomAttributes\Test\Unit\Block\Checkout;


class LayoutProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerCustomAttributes\Block\Checkout\LayoutProcessor
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $merger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeMapper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataMock;

    protected function setUp()
    {
        $this->merger = $this->getMock('\Magento\Checkout\Block\Checkout\AttributeMerger', [], [], '', false);
        $this->attributeMapper = $this->getMock(
            'Magento\Ui\Component\Form\AttributeMapper',
            [],
            [],
            '',
            false
        );
        $this->metadataMock = $this->getMock(
            'Magento\Customer\Model\AttributeMetadataDataProvider',
            [],
            [],
            '',
            false
        );

        $this->model = new \Magento\CustomerCustomAttributes\Block\Checkout\LayoutProcessor(
            $this->metadataMock,
            $this->attributeMapper,
            $this->merger
        );
    }

    public function testProcess()
    {
        $attributeMock = $this->getMock(
            '\Magento\Customer\Model\ResourceModel\Form\Attribute',
            ['getIsUserDefined', 'getAttributeCode'],
            [],
            '',
            false
        );
        $jsLayout = [];
        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
        ['children']['payment']['children']['payments-list']['children']['free_method']['children']
        ['form-fields']['children'] = [
            'fieldOne' => [
                'param' => 'value',
            ],
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
        ['children']['payment']['children']['payments-list']['children']['free_method']
        ['dataScopePrefix'] = 'freeshipping';

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'] = [
            'fieldOne' => ['param' => 'value'],
        ];
        $this->metadataMock->expects($this->once())
            ->method('loadAttributesCollection')
            ->with('customer_address', 'customer_register_address')
            ->willReturn([$attributeMock]);
        $attributeMock->expects($this->once())->method('getIsUserDefined')->willReturn(true);
        $attributeMock->expects($this->once())->method('getAttributeCode')->willReturn('code');
        $this->attributeMapper->expects($this->once())->method('map')->with($attributeMock);
        $this->merger->expects($this->exactly(2))->method('merge');

        $this->model->process($jsLayout);
    }
}
