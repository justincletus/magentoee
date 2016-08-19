<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCheckout\Test\Unit\Model;

use Magento\AdvancedCheckout\Helper\Data;

class CartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdvancedCheckout\Model\Cart
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeFormatMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemServiceMock;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productRepository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockRegistry;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $stockItemMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $stockState;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $stockHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $quoteMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $quoteRepositoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $quoteFactoryMock;

    protected function setUp()
    {
        $cartMock = $this->getMock('Magento\Checkout\Model\Cart', [], [], '', false);
        $messageFactoryMock = $this->getMock('Magento\Framework\Message\Factory', [], [], '', false);
        $eventManagerMock = $this->getMock('Magento\Framework\Event\ManagerInterface');
        $this->helperMock = $this->getMock('Magento\AdvancedCheckout\Helper\Data', [], [], '', false);
        $wishListFactoryMock = $this->getMock('Magento\Wishlist\Model\WishlistFactory', ['create'], [], '', false);
        $this->quoteMock = $this->getMock('Magento\Quote\Model\Quote', ['getStore', '__wakeup'], [], '', false);
        $this->quoteRepositoryMock = $this->getMock('\Magento\Quote\Api\CartRepositoryInterface');
        $this->storeManagerMock = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->localeFormatMock = $this->getMock('Magento\Framework\Locale\FormatInterface');
        $messageManagerMock = $this->getMock('Magento\Framework\Message\ManagerInterface');
        $customerSessionMock = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);

        $this->productRepository = $this->getMock('Magento\Catalog\Api\ProductRepositoryInterface');
        $optionFactoryMock = $this->getMock('Magento\Catalog\Model\Product\OptionFactory', ['create'], [], '', false);
        $prodTypesConfigMock = $this->getMock('Magento\Catalog\Model\ProductTypes\ConfigInterface', [], [], '', false);
        $cartConfigMock = $this->getMock('Magento\Catalog\Model\Product\CartConfiguration', [], [], '', false);

        $this->stockRegistry = $this->getMockBuilder('Magento\CatalogInventory\Model\StockRegistry')
            ->disableOriginalConstructor()
            ->setMethods(['getStockItem', '__wakeup'])
            ->getMock();

        $this->stockItemMock = $this->getMock(
            'Magento\CatalogInventory\Model\Stock\Item',
            ['getQtyIncrements', 'getIsInStock', '__wakeup', 'getMaxSaleQty', 'getMinSaleQty'],
            [],
            '',
            false
        );

        $this->stockRegistry->expects($this->any())
            ->method('getStockItem')
            ->will($this->returnValue($this->stockItemMock));

        $this->stockState = $this->getMock(
            'Magento\CatalogInventory\Model\StockState',
            [],
            [],
            '',
            false
        );

        $this->stockHelper = $this->getMock(
            'Magento\CatalogInventory\Helper\Stock',
            [],
            [],
            '',
            false
        );

        $this->quoteFactoryMock = $this->getMock('\Magento\Quote\Model\QuoteFactory', ['create'], [], '', false);

        $this->model = new \Magento\AdvancedCheckout\Model\Cart(
            $cartMock,
            $messageFactoryMock,
            $eventManagerMock,
            $this->helperMock,
            $optionFactoryMock,
            $wishListFactoryMock,
            $this->quoteRepositoryMock,
            $this->storeManagerMock,
            $this->localeFormatMock,
            $messageManagerMock,
            $prodTypesConfigMock,
            $cartConfigMock,
            $customerSessionMock,
            $this->stockRegistry,
            $this->stockState,
            $this->stockHelper,
            $this->productRepository,
            $this->quoteFactoryMock
        );
    }

    /**
     * @param string $sku
     * @param array $config
     * @param array $expectedResult
     *
     * @covers \Magento\AdvancedCheckout\Model\Cart::__construct
     * @covers \Magento\AdvancedCheckout\Model\Cart::setAffectedItemConfig
     * @covers \Magento\AdvancedCheckout\Model\Cart::getAffectedItemConfig
     * @dataProvider setAffectedItemConfigDataProvider
     */
    public function testSetAffectedItemConfig($sku, $config, $expectedResult)
    {
        $this->model->setAffectedItemConfig($sku, $config);
        $this->assertEquals($expectedResult, $this->model->getAffectedItemConfig($sku));
    }

    /**
     * @return array
     */
    public function setAffectedItemConfigDataProvider()
    {
        return [
            [
                'sku' => 123,
                'config' => ['1'],
                'expectedResult' => [1]
            ],
            [
                'sku' => 0,
                'config' => ['1'],
                'expectedResult' => [1]
            ],
            [
                'sku' => 'aaa',
                'config' => ['1'],
                'expectedResult' => [1]
            ],
            [
                'sku' => '',
                'config' => ['1'],
                'expectedResult' => []
            ],
            [
                'sku' => false,
                'config' => ['1'],
                'expectedResult' => [1]
            ],
            [
                'sku' => null,
                'config' => ['1'],
                'expectedResult' => [1]
            ],
            [
                'sku' => 'aaa',
                'config' => [],
                'expectedResult' => []
            ],
            [
                'sku' => 'aaa',
                'config' => null,
                'expectedResult' => []
            ],
            [
                'sku' => 'aaa',
                'config' => false,
                'expectedResult' => []
            ],
            [
                'sku' => 'aaa',
                'config' => 0,
                'expectedResult' => []
            ],
            [
                'sku' => 'aaa',
                'config' => '',
                'expectedResult' => []
            ]
        ];
    }

    /**
     * @param string $sku
     * @param integer $qty
     * @param string $expectedCode
     *
     * @dataProvider prepareAddProductsBySkuDataProvider
     * @covers \Magento\AdvancedCheckout\Model\Cart::_getValidatedItem
     * @covers \Magento\AdvancedCheckout\Model\Cart::_loadProductBySku
     * @covers \Magento\AdvancedCheckout\Model\Cart::checkItem
     */
    public function testGetValidatedItem($sku, $qty, $expectedCode)
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', ['getId', 'getWebsiteId'], [], '', false);
        $storeMock->expects($this->any())->method('getStore')->will($this->returnValue(1));
        $storeMock->expects($this->any())->method('getWebsiteId')->will($this->returnValue(1));

        $sessionMock = $this->getMock(
            'Magento\Framework\Session\SessionManager',
            ['getAffectedItems', 'setAffectedItems'],
            [],
            '',
            false
        );
        $sessionMock->expects($this->any())->method('getAffectedItems')->will($this->returnValue([]));

        $productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['getId', 'getWebsiteIds', 'isComposite', '__wakeup', '__sleep'],
            [],
            '',
            false
        );
        $productMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $productMock->expects($this->any())->method('getWebsiteIds')->will($this->returnValue([1]));
        $productMock->expects($this->any())->method('isComposite')->will($this->returnValue(false));

        $this->productRepository->expects($this->any())->method('get')->with($sku)
            ->will($this->returnValue($productMock));
        $this->helperMock->expects($this->any())->method('getSession')->will($this->returnValue($sessionMock));
        $this->localeFormatMock->expects($this->any())->method('getNumber')->will($this->returnArgument(0));
        $this->storeManagerMock->expects($this->any())->method('getStore')->will($this->returnValue($storeMock));
        $item = $this->model->checkItem($sku, $qty);

        $this->assertTrue($item['code'] == $expectedCode);
    }

    /**
     * @return array
     */
    public function prepareAddProductsBySkuDataProvider()
    {
        return [
            [
                'sku' => 'aaa',
                'qty' => 2,
                'expectedCode' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS,
            ],
            [
                'sku' => 'aaa',
                'qty' => 'aaa',
                'expectedCode' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_INVALID_NUMBER,
            ],
            [
                'sku' => 'aaa',
                'qty' => -1,
                'expectedCode' =>
                    \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_INVALID_NON_POSITIVE,
            ],
            [
                'sku' => 'aaa',
                'qty' => 0.00001,
                'expectedCode' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_INVALID_RANGE,
            ],
            [
                'sku' => 'aaa',
                'qty' => 100000000.0,
                'expectedCode' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_INVALID_RANGE,
            ],
            [
                'sku' => 'a',
                'qty' => 2,
                'expectedCode' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS,
            ],
            [
                'sku' => 123,
                'qty' => 2,
                'expectedCode' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS,
            ],
            [
                'sku' => 0,
                'qty' => 2,
                'expectedCode' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS,
            ],
            [
                'sku' => '',
                'qty' => 2,
                'expectedCode' => \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_EMPTY,
            ]
        ];
    }

    /**
     * @param array $config
     * @param array $result
     * @dataProvider getQtyStatusDataProvider
     * @TODO refactor me
     */
    public function testGetQtyStatus($config, $result)
    {
        $websiteId = 10;
        $productId = $config['product_id'];
        $requestQty = $config['request_qty'];

        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $store->expects($this->any())
            ->method('getWebsiteId')
            ->will($this->returnValue($websiteId));
        $this->quoteMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));


        $this->quoteMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        $this->quoteFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->quoteMock));

        $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $product->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($productId));

        $resultObject = new \Magento\Framework\DataObject($config['result']);
        $this->stockState->expects($this->once())
            ->method('checkQuoteItemQty')
            ->with(
                $this->equalTo($productId),
                $this->equalTo($requestQty),
                $this->equalTo($requestQty),
                $this->equalTo($requestQty),
                $this->equalTo($websiteId)
            )
            ->will($this->returnValue($resultObject));

        if ($config['result']['has_error']) {
            switch ($resultObject->getErrorCode()) {
                case 'qty_increments':
                    $this->stockItemMock->expects($this->once())
                        ->method('getQtyIncrements')
                        ->will($this->returnValue($config['result']['qty_increments']));
                    break;
                case 'qty_min':
                    $this->stockItemMock->expects($this->once())
                        ->method('getMinSaleQty')
                        ->will($this->returnValue($config['result']['qty_min_allowed']));
                    break;
                case 'qty_max':
                    $this->stockItemMock->expects($this->once())
                        ->method('getMaxSaleQty')
                        ->will($this->returnValue($config['result']['qty_max_allowed']));
                    break;
                default:
                    $this->stockState->expects($this->once())
                        ->method('getStockQty')
                        ->with($this->equalTo($productId))
                        ->will($this->returnValue($config['result']['qty_max_allowed']));
                    break;
            }
        }
        $this->assertSame($result, $this->model->getQtyStatus($product, $requestQty));
    }

    /**
     * @return array
     */
    public function getQtyStatusDataProvider()
    {
        return [
            'error qty_increments' => [
                [
                    'product_id' => 11,
                    'request_qty' => 6,
                    'result' => [
                        'has_error' => true,
                        'error_code' => 'qty_increments',
                        'qty_increments' => 1,
                        'message' => 'hello qty_increments'
                    ]
                ],
                [
                    'qty_increments' => 1,
                    'status' => Data::ADD_ITEM_STATUS_FAILED_QTY_INCREMENTS,
                    'error' => 'hello qty_increments'
                ]
            ],
            'error qty_min' => [
                [
                    'product_id' => 14,
                    'request_qty' => 5,
                    'result' => [
                        'has_error' => true,
                        'error_code' => 'qty_min',
                        'qty_min_allowed' => 2,
                        'message' => 'hello qty_min_allowed'
                    ]
                ],
                [
                    'qty_min_allowed' => 2,
                    'status' => Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED_IN_CART,
                    'error' => 'hello qty_min_allowed'
                ]
            ],
            'error qty_max' => [
                [
                    'product_id' => 13,
                    'request_qty' => 4,
                    'result' => [
                        'has_error' => true,
                        'error_code' => 'qty_max',
                        'qty_max_allowed' => 3,
                        'message' => 'hello qty_max_allowed'
                    ]
                ],
                [
                    'qty_max_allowed' => 3,
                    'status' => Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED_IN_CART,
                    'error' => 'hello qty_max_allowed'
                ]
            ],
            'error default' => [
                [
                    'product_id' => 12,
                    'request_qty' => 3,
                    'result' => [
                        'has_error' => true,
                        'error_code' => 'default',
                        'qty_max_allowed' => 4,
                        'message' => 'hello default'
                    ]
                ],
                [
                    'qty_max_allowed' => 4,
                    'status' => Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED,
                    'error' => 'hello default'
                ]
            ],
            'no error' => [
                [
                    'product_id' => 18,
                    'request_qty' => 22,
                    'result' => ['has_error' => false]
                ],
                true
            ],
        ];
    }
}
