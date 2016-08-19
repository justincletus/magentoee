<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Test\Unit\Model\Plugin;

use Magento\CustomerCustomAttributes\Model\Plugin\ConvertQuoteAddressToOrderAddress;

class ConvertQuoteAddressToOrderAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConvertQuoteAddressToOrderAddress
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $customerDataMock;

    protected function setUp()
    {
        $this->customerDataMock = $this->getMock(
            \Magento\CustomerCustomAttributes\Helper\Data::class,
            [],
            [],
            '',
            false
        );
        $this->model = new ConvertQuoteAddressToOrderAddress($this->customerDataMock);
    }

    public function testAroundConvert()
    {
        $attribute = 'attribute';
        $attributeValue = 'attributeValue';
        $quoteAddressMock = $this->getMock(\Magento\Quote\Model\Quote\Address::class, [], [], '', '', false);
        $orderAddressMock = $this->getMock(\Magento\Sales\Model\Order\Address::class, [], [], '', '', false);

        $this->customerDataMock->expects($this->once())
            ->method('getCustomerAddressUserDefinedAttributeCodes')
            ->willReturn([$attribute]);

        $quoteAddressMock->expects($this->once())->method('getData')->with($attribute)->willReturn($attributeValue);
        $orderAddressMock->expects($this->once())
            ->method('setData')
            ->with($attribute, $attributeValue)
            ->willReturnSelf();

        $closure = function () use ($orderAddressMock) {
            return $orderAddressMock;
        };

        $result = $this->model->aroundConvert(
            $this->getMock(\Magento\Quote\Model\Quote\Address\ToOrderAddress::class, [], [], '', false),
            $closure,
            $quoteAddressMock
        );

        $this->assertEquals($orderAddressMock, $result);
    }
}
