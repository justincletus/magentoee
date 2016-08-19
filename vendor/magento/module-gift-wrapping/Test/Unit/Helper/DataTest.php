<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Test\Unit\Helper;

use Magento\GiftWrapping\Model\System\Config\Source\Display\Type;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteDetailsItemFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteDetailsFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxCalculationService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceCurrency;

    /**
     * @var \Magento\GiftWrapping\Helper\Data
     */
    protected $subject;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $className = 'Magento\GiftWrapping\Helper\Data';
        $arguments = $objectManager->getConstructArguments($className);
        /** @var \Magento\Framework\App\Helper\Context $context */
        $context = $arguments['context'];
        $this->scopeConfigMock = $context->getScopeConfig();
        $this->storeManager = $arguments['storeManager'];
        $this->quoteDetailsItemFactory = $arguments['quoteDetailsItemFactory'];
        $this->quoteDetailsFactory = $arguments['quoteDetailsFactory'];
        $this->taxCalculationService = $arguments['taxCalculationService'];
        $this->priceCurrency = $arguments['priceCurrency'];
        $this->subject = $objectManager->getObject($className, $arguments);
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGetPrice()
    {
        $storeId = 2;
        $taxClassKeyValue = 13;
        $item = $this->getMock('Magento\Framework\DataObject', ['getTaxClassKey'], [], '', false);
        $price = 12.45;
        $includeTax = true;
        $shippingAddress = $this->getMock('Magento\Quote\Model\Quote\Address', [], [], '', false);
        $billingAddress = $this->getMock('Magento\Quote\Model\Quote\Address', [], [], '', false);
        $shippingDataModel = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\AddressInterface',
            [],
            'shippingDataModel',
            false
        );
        $billingDataModel = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\AddressInterface',
            [],
            'billingDataMode',
            false
        );

        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $store->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->willReturn($store);

        $taxClassKey = $this->getMockForAbstractClass('Magento\Tax\Api\Data\TaxClassKeyInterface', [], '', false);
        $taxClassKey->expects($this->once())
            ->method('getValue')
            ->willReturn($taxClassKeyValue);
        $item->expects($this->once())
            ->method('getTaxClassKey')
            ->willReturn($taxClassKey);

        $shippingAddress->expects($this->once())
            ->method('getDataModel')
            ->willReturn($shippingDataModel);
        $billingAddress->expects($this->once())
            ->method('getDataModel')
            ->willReturn($billingDataModel);

        $quoteDetailsItem = $this->getMockForAbstractClass(
            'Magento\Tax\Api\Data\QuoteDetailsItemInterface',
            [],
            '',
            false
        );
        $quoteDetailsItem->expects($this->once())
            ->method('setQuantity')
            ->with(1)
            ->willReturnSelf();
        $quoteDetailsItem->expects($this->once())
            ->method('setCode')
            ->with('giftwrapping_code')
            ->willReturnSelf();
        $quoteDetailsItem->expects($this->once())
            ->method('setTaxClassId')
            ->with($taxClassKeyValue)
            ->willReturnSelf();
        $quoteDetailsItem->expects($this->once())
            ->method('setIsTaxIncluded')
            ->with(false)
            ->willReturnSelf();
        $quoteDetailsItem->expects($this->once())
            ->method('setType')
            ->with('giftwrapping_type')
            ->willReturnSelf();
        $quoteDetailsItem->expects($this->once())
            ->method('setTaxClassKey')
            ->with($taxClassKey)
            ->willReturnSelf();
        $quoteDetailsItem->expects($this->once())
            ->method('setUnitPrice')
            ->with($price)
            ->willReturnSelf();

        $this->quoteDetailsItemFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($quoteDetailsItem);
        $quoteDetails = $this->getMockForAbstractClass(
            'Magento\Tax\Api\Data\QuoteDetailsInterface',
            [],
            '',
            false
        );
        $quoteDetails->expects($this->once())
            ->method('setShippingAddress')
            ->with($shippingDataModel)
            ->willReturnSelf();
        $quoteDetails->expects($this->once())
            ->method('setBillingAddress')
            ->with($billingDataModel)
            ->willReturnSelf();
        $quoteDetails->expects($this->once())
            ->method('setCustomerTaxClassId')
            ->with(null)
            ->willReturnSelf();
        $quoteDetails->expects($this->once())
            ->method('setItems')
            ->with([$quoteDetailsItem])
            ->willReturnSelf();
        $this->quoteDetailsFactory->expects($this->once())
            ->method('create')
            ->willReturn($quoteDetails);

        $taxDetailItem = $this->getMockForAbstractClass('Magento\Tax\Api\Data\TaxDetailsItemInterface', [], '', false);
        $taxDetailItem->expects($this->once())
            ->method('getPriceInclTax')
            ->willReturn($price);
        $taxDetail = $this->getMockForAbstractClass('Magento\Tax\Api\Data\TaxDetailsInterface', [], '', false);
        $taxDetail->expects($this->once())
            ->method('getItems')
            ->willReturn([$taxDetailItem]);
        $this->taxCalculationService->expects($this->once())
            ->method('calculateTax')
            ->with($quoteDetails, $storeId, true)
            ->willReturn($taxDetail);

        $this->subject->getPrice($item, $price, $includeTax, $shippingAddress, $billingAddress);
    }

    public function testGetPriceWithoutTaxCalculation()
    {
        $item = $this->getMock('Magento\Framework\DataObject', ['getTaxClassKey'], [], '', false);
        $price = 12;
        $includeTax = false;
        $shippingAddress = $this->getMock('Magento\Quote\Model\Quote\Address', [], [], '', false);
        $billingAddress = $this->getMock('Magento\Quote\Model\Quote\Address', [], [], '', false);

        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->willReturn($store);

        $taxClassKey = $this->getMockForAbstractClass('Magento\Tax\Api\Data\TaxClassKeyInterface', [], '', false);
        $item->expects($this->once())
            ->method('getTaxClassKey')
            ->willReturn($taxClassKey);

        $this->priceCurrency
            ->expects($this->once())
            ->method('round')
            ->with($price)
            ->willReturn($price);

        $this->subject->getPrice($item, $price, $includeTax, $shippingAddress, $billingAddress);
    }

    public function testIsGiftWrappingAvailableIfProductConfigIsNull()
    {
        $scopeConfig = 'scope_config';
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_ALLOWED_FOR_ITEMS,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertEquals($scopeConfig, $this->subject->isGiftWrappingAvailableForProduct(null, $storeMock));
    }

    public function testIsGiftWrappingAvailableIfProductConfigIsEmpty()
    {
        $scopeConfig = 'scope_config';
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_ALLOWED_FOR_ITEMS,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertEquals($scopeConfig, $this->subject->isGiftWrappingAvailableForProduct('', $storeMock));
    }

    public function testIsGiftWrappingAvailableIfProductConfigExists()
    {
        $productConfig = ['option' => 'config'];
        $this->assertEquals($productConfig, $this->subject->isGiftWrappingAvailableForProduct($productConfig));
    }

    public function testIsGiftWrappingAvailableForItems()
    {
        $scopeConfig = 'scope_config';
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_ALLOWED_FOR_ITEMS,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertEquals($scopeConfig, $this->subject->isGiftWrappingAvailableForItems($storeMock));
    }

    public function testIsGiftWrappingAvailableForOrder()
    {
        $scopeConfig = 'scope_config';
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_ALLOWED_FOR_ORDER,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertEquals($scopeConfig, $this->subject->isGiftWrappingAvailableForOrder($storeMock));
    }

    public function testGetWrappingTaxClass()
    {
        $scopeConfig = 'scope_config';
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_TAX_CLASS,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertEquals($scopeConfig, $this->subject->getWrappingTaxClass($storeMock));
    }

    public function testAllowPrintedCard()
    {
        $scopeConfig = 'scope_config';
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_ALLOW_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertEquals($scopeConfig, $this->subject->allowPrintedCard($storeMock));
    }

    public function testAllowGiftReceipt()
    {
        $scopeConfig = 'scope_config';
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_ALLOW_GIFT_RECEIPT,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertEquals($scopeConfig, $this->subject->allowGiftReceipt($storeMock));
    }

    public function testGetPrintedCardPrice()
    {
        $scopeConfig = 'scope_config';
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRINTED_CARD_PRICE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue($scopeConfig));
        $this->assertEquals($scopeConfig, $this->subject->getPrintedCardPrice($storeMock));
    }

    public function testDisplayCartWrappingIncludeTaxPriceWhenDisplayTypeIsBoth()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_BOTH));
        $this->assertTrue($this->subject->displayCartWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartWrappingIncludeTaxPriceWhenDisplayTypeIsIncludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_INCLUDING_TAX));
        $this->assertTrue($this->subject->displayCartWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartWrappingIncludeTaxPriceWhenDisplayTypeIsExcludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_EXCLUDING_TAX));
        $this->assertFalse($this->subject->displayCartWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartWrappingExcludeTaxPriceWhenDisplayTypeIsIncludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_INCLUDING_TAX));
        $this->assertFalse($this->subject->displayCartWrappingExcludeTaxPrice($storeMock));
    }

    public function testDisplayCartWrappingExcludeTaxPriceWhenDisplayTypeIsExcludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_EXCLUDING_TAX));
        $this->assertTrue($this->subject->displayCartWrappingExcludeTaxPrice($storeMock));
    }

    public function testDisplayCartWrappingBothPricesIsIncludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_EXCLUDING_TAX));
        $this->assertFalse($this->subject->displayCartWrappingBothPrices($storeMock));
    }

    public function testDisplayCartWrappingBothPricesIsBoth()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_BOTH));
        $this->assertTrue($this->subject->displayCartWrappingBothPrices($storeMock));
    }

    public function testDisplayCartCardIncludeTaxPriceIsBoth()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_BOTH));
        $this->assertTrue($this->subject->displayCartCardIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartCardIncludeTaxPriceIsExcludingTaxPrice()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_EXCLUDING_TAX));
        $this->assertFalse($this->subject->displayCartCardIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartCardIncludeTaxPriceIsIncludingTaxPrice()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_INCLUDING_TAX));
        $this->assertTrue($this->subject->displayCartCardIncludeTaxPrice($storeMock));
    }

    public function testDisplayCartCardBothPricesIncludingTaxPrice()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_INCLUDING_TAX));
        $this->assertFalse($this->subject->displayCartCardBothPrices($storeMock));
    }

    public function testDisplayCartCardBothPricesDisplayTypeBoth()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_BOTH));
        $this->assertTrue($this->subject->displayCartCardBothPrices($storeMock));
    }

    public function testDisplaySalesWrappingIncludeTaxPriceDisplayTypeBoth()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_BOTH));
        $this->assertTrue($this->subject->displaySalesWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesWrappingIncludeTaxPriceDisplayTypeIncludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_INCLUDING_TAX));
        $this->assertTrue($this->subject->displaySalesWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesWrappingIncludeTaxPriceDisplayTypeExcludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_EXCLUDING_TAX));
        $this->assertFalse($this->subject->displaySalesWrappingIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesWrappingExcludeTaxPriceDisplayTypeExcludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_EXCLUDING_TAX));
        $this->assertTrue($this->subject->displaySalesWrappingExcludeTaxPrice($storeMock));
    }

    public function testDisplaySalesWrappingExcludeTaxPriceDisplayTypeIncludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_INCLUDING_TAX));
        $this->assertFalse($this->subject->displaySalesWrappingExcludeTaxPrice($storeMock));
    }

    public function testDisplaySalesWrappingBothPricesDisplayTypeIncludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_INCLUDING_TAX));
        $this->assertFalse($this->subject->displaySalesWrappingBothPrices($storeMock));
    }

    public function testDisplaySalesWrappingBothPricesDisplayTypeBoth()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_BOTH));
        $this->assertTrue($this->subject->displaySalesWrappingBothPrices($storeMock));
    }

    public function testDisplaySalesCardIncludeTaxPriceDisplayTypeBoth()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_BOTH));
        $this->assertTrue($this->subject->displaySalesCardIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesCardIncludeTaxPriceDisplayTypeIncludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_INCLUDING_TAX));
        $this->assertTrue($this->subject->displaySalesCardIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesCardIncludeTaxPriceDisplayTypeExcludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_EXCLUDING_TAX));
        $this->assertFalse($this->subject->displaySalesCardIncludeTaxPrice($storeMock));
    }

    public function testDisplaySalesCardBothPricesDisplayTypeExcludingTax()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_EXCLUDING_TAX));
        $this->assertFalse($this->subject->displaySalesCardBothPrices($storeMock));
    }

    public function testDisplaySalesCardBothPricesDisplayTypeBoth()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\GiftWrapping\Helper\Data::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(Type::DISPLAY_TYPE_BOTH));
        $this->assertTrue($this->subject->displaySalesCardBothPrices($storeMock));
    }
}
