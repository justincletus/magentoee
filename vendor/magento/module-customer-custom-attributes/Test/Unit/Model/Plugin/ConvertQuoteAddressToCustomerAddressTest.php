<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Test\Unit\Model\Plugin;

use Magento\CustomerCustomAttributes\Model\Plugin\ConvertQuoteAddressToCustomerAddress;

class ConvertQuoteAddressToCustomerAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConvertQuoteAddressToCustomerAddress
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
        $this->model = new ConvertQuoteAddressToCustomerAddress($this->customerDataMock);
    }

    public function testAfterExportCustomerAddress()
    {
        $attribute = 'attribute';
        $attributeValue = 'attributeValue';
        $quoteAddressMock = $this->getMock(\Magento\Quote\Model\Quote\Address::class, [], [], '', '', false);
        $customerAddressMock = $this->getMock(\Magento\Customer\Api\Data\AddressInterface::class);

        $this->customerDataMock->expects($this->once())
            ->method('getCustomerAddressUserDefinedAttributeCodes')
            ->willReturn([$attribute]);

        $quoteAddressMock->expects($this->once())->method('getData')->with($attribute)->willReturn($attributeValue);
        $customerAddressMock->expects($this->once())
            ->method('setCustomAttribute')
            ->with($attribute, $attributeValue)
            ->willReturnSelf();

        $this->assertEquals(
            $customerAddressMock,
            $this->model->afterExportCustomerAddress($quoteAddressMock, $customerAddressMock)
        );
    }
}
