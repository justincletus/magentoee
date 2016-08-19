<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PricePermissions\Test\Unit\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

use Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\PricePermissions;

class NewObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NewObject
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pricePerDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    protected function setUp()
    {
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->requestMock = $this->getMock('\Magento\Framework\App\RequestInterface');
        $this->pricePerDataMock = $this->getMock('\Magento\PricePermissions\Helper\Data', [], [], '', false);
        $this->productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            [
                '__wakeup',
                'isObjectNew',
                'getTypeId',
                'getPriceType',
                'setPrice',
                'setGiftcardAmounts',
                'setMsrpEnabled',
                'setMsrpDisplayActualPriceType'
            ],
            [],
            '',
            false
        );

        $this->pricePerDataMock->expects(
            $this->once()
        )->method(
            'getDefaultProductPriceString'
        )->will(
            $this->returnValue('0.00')
        );

        $this->model = new \Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler\NewObject($this->storeManagerMock, $this->requestMock, $this->pricePerDataMock);
    }

    public function testHandleWithNotNewProduct()
    {
        $this->productMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(false));
        $this->model->handle($this->productMock);
    }

    public function testHandleWithDynamicProductPrice()
    {
        $this->productMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(true));
        $this->productMock->expects(
            $this->once()
        )->method(
            'getTypeId'
        )->will(
            $this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE)
        );
        $this->productMock->expects(
            $this->once()
        )->method(
            'getPriceType'
        )->will(
            $this->returnValue(\Magento\Bundle\Model\Product\Price::PRICE_TYPE_DYNAMIC)
        );

        $this->productMock->expects($this->never())->method('setPrice');

        $this->productMock->expects(
            $this->once()
        )->method(
            'setMsrpDisplayActualPriceType'
        )->with(
            \Magento\Msrp\Model\Product\Attribute\Source\Type\Price::TYPE_USE_CONFIG
        );

        $this->model->handle($this->productMock);
    }

    public function testHandleWithGiftCardProductType()
    {
        $this->productMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(true));
        $this->productMock->expects(
            $this->any()
        )->method(
            'getTypeId'
        )->will(
            $this->returnValue(\Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD)
        );

        $this->productMock->expects($this->once())->method('setPrice')->with('0.0');

        $this->requestMock->expects($this->once())->method('getParam')->with('store')->will($this->returnValue(10));
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue(5));
        $this->storeManagerMock->expects(
            $this->once()
        )->method(
            'getStore'
        )->with(
            10
        )->will(
            $this->returnValue($storeMock)
        );

        $this->productMock->expects(
            $this->once()
        )->method(
            'setGiftcardAmounts'
        )->with(
            [['website_id' => 5, 'price' => 0.0, 'delete' => '']]
        );
        $this->productMock->expects(
            $this->once()
        )->method(
            'setMsrpDisplayActualPriceType'
        )->with(
            \Magento\Msrp\Model\Product\Attribute\Source\Type\Price::TYPE_USE_CONFIG
        );

        $this->model->handle($this->productMock);
    }

    public function testHandleWithNonGiftCardProductType()
    {
        $this->productMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(true));
        $this->productMock->expects($this->any())->method('getTypeId')->will($this->returnValue('some product type'));

        $this->productMock->expects($this->once())->method('setPrice')->with('0.0');

        $this->productMock->expects($this->never())->method('setGiftcardAmounts');

        $this->productMock->expects(
            $this->once()
        )->method(
            'setMsrpDisplayActualPriceType'
        )->with(
            \Magento\Msrp\Model\Product\Attribute\Source\Type\Price::TYPE_USE_CONFIG
        );

        $this->model->handle($this->productMock);
    }
}
