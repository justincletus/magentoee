<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Test\Unit\Block\Adminhtml\Rma\Edit\Tab\General\Shipping;

/**
 * Class MethodsTest
 */
class MethodsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping\Methods
     */
    protected $methods;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceCurrencyMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxDataMock;

    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->priceCurrencyMock = $this->getMockBuilder('Magento\Directory\Model\PriceCurrency')
            ->disableOriginalConstructor()
            ->getMock();
        $this->taxDataMock = $this->getMockBuilder('Magento\Tax\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(['getShippingPrice'])
            ->getMock();
        $this->methods = $objectManager->getObject(
            'Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping\Methods',
            [
                'priceCurrency' => $this->priceCurrencyMock,
                'taxData' => $this->taxDataMock
            ]
        );
    }

    public function testGetShippingPrice()
    {
        $price = 100;
        $expected = 100.00;

        $this->taxDataMock->expects($this->once())
            ->method('getShippingPrice')
            ->with($price)
            ->willReturnArgument(0);
        $this->priceCurrencyMock->expects($this->once())
            ->method('convert')
            ->with($price, true, false)
            ->willReturn($expected);
        $this->assertEquals($expected, $this->methods->getShippingPrice($price));
    }
}
