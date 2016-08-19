<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\PricePermissions\Test\Unit\Observer;

class AdminhtmlBlockHtmlBeforeObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PricePermissions\Observer\AdminhtmlBlockHtmlBeforeObserver
     */
    protected $_observer;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    protected $_varienObserver;

    /**
     * @var \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected $_block;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\PricePermissions\Observer\ObserverData|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerData;

    protected function setUp()
    {
        $this->_registry = $this->getMock(
            'Magento\Framework\Registry',
            ['registry'],
            [],
            '',
            false
        );
        $this->_request = $this->getMock(
            'Magento\Framework\App\RequestInterface',
            [],
            [],
            '',
            false,
            false
        );
        $this->_storeManager = $this->getMock(
            'Magento\Store\Model\StoreManagerInterface',
            [],
            [],
            '',
            false,
            false
        );

        $this->observerData = $this->getMock(
            'Magento\PricePermissions\Observer\ObserverData',
            [],
            [],
            '',
            false
        );
        $this->observerData->expects($this->any())->method('isCanEditProductPrice')->willReturn(false);
        $this->observerData->expects($this->any())->method('isCanReadProductPrice')->willReturn(false);
        $this->observerData->expects($this->any())->method('canEditProductStatus')->willReturn(false);
        $this->observerData->expects($this->any())->method('getDefaultProductPriceString')->willReturn('default');

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $constructArguments = $objectManager->getConstructArguments(
            'Magento\PricePermissions\Observer\AdminhtmlBlockHtmlBeforeObserver',
            [
                'coreRegistry' => $this->_registry,
                'request' => $this->_request,
                'storeManager' => $this->_storeManager,
                'observerData' => $this->observerData,
            ]
        );

        $this->_observer = $this->getMock(
            'Magento\PricePermissions\Observer\AdminhtmlBlockHtmlBeforeObserver',
            ['_removeColumnFromGrid'],
            $constructArguments
        );
        $this->_block = $this->getMock(
            'Magento\Backend\Block\Widget\Grid',
            [
                'getNameInLayout',
                'getMassactionBlock',
                'setCanReadPrice',
                'setCanEditPrice',
                'setTabData',
                'getChildBlock',
                'getParentBlock',
                'setDefaultProductPrice',
                'getForm',
                'getGroup',
            ],
            [],
            '',
            false
        );
        $this->_varienObserver = $this->getMock('Magento\Framework\Event\Observer', ['getBlock', 'getEvent']);
        $this->_varienObserver->expects($this->any())->method('getBlock')->willReturn($this->_block);
    }

    /**
     * @param $blockName string
     * @dataProvider productGridMassactionDataProvider
     */
    public function testAdminhtmlBlockHtmlBeforeProductGridMassaction($blockName)
    {
        $massaction = $this->getMock(
            'Magento\Backend\Block\Widget\Grid\Massaction',
            ['removeItem'],
            [],
            '',
            false
        );
        $massaction->expects($this->once())->method('removeItem')->with($this->equalTo('status'));

        $this->_setGetNameInLayoutExpects($blockName);
        $this->_block->expects($this->once())->method('getMassactionBlock')->willReturn($massaction);
        $this->_assertPriceColumnRemove();

        $this->_observer->execute($this->_varienObserver);
    }

    /**
     * @param $blockName string
     * @dataProvider gridCategoryProductGridDataProvider
     */
    public function testAdminhtmlBlockHtmlBeforeGridCategoryProductGrid($blockName)
    {
        $this->_setGetNameInLayoutExpects($blockName);

        $this->_assertPriceColumnRemove();
        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeCustomerViewCart()
    {
        $this->_setGetNameInLayoutExpects('admin.customer.view.cart');

        $this->_observer->expects(
            $this->exactly(2)
        )->method(
            '_removeColumnFromGrid'
        )->with(
            $this->isInstanceOf('Magento\Backend\Block\Widget\Grid'),
            $this->logicalOr($this->equalTo('price'), $this->equalTo('total'))
        );
        $this->_observer->execute($this->_varienObserver);
    }

    /**
     * @param $blockName string
     * @dataProvider checkoutAccordionDataProvider
     */
    public function testAdminhtmlBlockHtmlBeforeCheckoutAccordion($blockName)
    {
        $this->_setGetNameInLayoutExpects($blockName);

        $this->_assertPriceColumnRemove();
        $this->_observer->execute($this->_varienObserver);
    }

    /**
     * @param $blockName string
     * @dataProvider checkoutItemsDataProvider
     */
    public function testAdminhtmlBlockHtmlBeforeItems($blockName)
    {
        $this->_setGetNameInLayoutExpects($blockName);
        $this->_block->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));
        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeDownloadableLinks()
    {
        $this->_setGetNameInLayoutExpects('catalog.product.edit.tab.downloadable.links');
        $this->_block->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setCanEditPrice')->with($this->equalTo(false));
        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeSuperConfigGrid()
    {
        $this->_setGetNameInLayoutExpects('admin.product.edit.tab.super.config.grid');

        $this->_assertPriceColumnRemove();
        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeTabSuperGroup()
    {
        $this->_setGetNameInLayoutExpects('catalog.product.edit.tab.super.group');

        $this->_assertPriceColumnRemove();
        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeProductOptions()
    {
        $this->_setGetNameInLayoutExpects('admin.product.options');

        $childBlock = $this->getMock(
            'Magento\Backend\Block\Template',
            ['setCanEditPrice', 'setCanReadPrice'],
            [],
            '',
            false
        );
        $childBlock->expects($this->once())->method('setCanEditPrice')->with($this->equalTo(false));
        $childBlock->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));

        $this->_block->expects(
            $this->once()
        )->method(
            'getChildBlock'
        )->with(
            $this->equalTo('options_box')
        )->will(
            $this->returnValue($childBlock)
        );

        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeBundleSearchGrid()
    {
        $this->_setGetNameInLayoutExpects('adminhtml.catalog.product.edit.tab.bundle.option.search.grid');

        $this->_assertPriceColumnRemove();
        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeBundlePrice()
    {
        $this->_setGetNameInLayoutExpects('adminhtml.catalog.product.bundle.edit.tab.attributes.price');
        $this->_block->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setCanEditPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setDefaultProductPrice')->with($this->equalTo('default'));
        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeBundleOpt()
    {
        $childBlock = $this->getMock(
            'Magento\Backend\Block\Template',
            ['setCanEditPrice', 'setCanReadPrice'],
            [],
            '',
            false
        );
        $this->_setGetNameInLayoutExpects('adminhtml.catalog.product.edit.tab.bundle.option');
        $childBlock->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));
        $childBlock->expects($this->once())->method('setCanEditPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setCanEditPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('getChildBlock')->willReturn($childBlock);
        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeTabAttributes()
    {
        $this->_setGetNameInLayoutExpects('product_tabs_attributes_tab');

        $product = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['__wakeup', 'getTypeId', 'isObjectNew'],
            [],
            '',
            false
        );
        $product->expects($this->any())
            ->method('getTypeId')
            ->willReturn(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $product->expects($this->any())
            ->method('isObjectNew')
            ->willReturn(true);
        $this->_registry
            ->expects($this->any())
            ->method('registry')
            ->with($this->equalTo('product'))
            ->willReturn($product);
        $form = $this->getMock(
            '\Magento\Framework\Data\Form',
            ['getElement', 'setReadonly'],
            [],
            '',
            false
        );
        $form->expects($this->any())
            ->method('setReadonly')
            ->with($this->equalTo(true), $this->equalTo(true))
            ->will($this->returnSelf());
        $fieldsetGroup = $this->getMock(
            '\Magento\Framework\Data\Form\Element\Fieldset',
            ['removeField'],
            [],
            '',
            false
        );
        $fieldsetGroup->expects($this->any())->method('removeField')->will($this->returnSelf());
        $giftcardAmounts = $this->getMock(
            'Magento\Framework\Data\Form\Element\AbstractElement',
            ['setValue'],
            [],
            '',
            false
        );
        $giftcardAmountsValue = [
            [
                'website_id' => 1,
                'value' => 'default',
                'website_value' => 0
            ]
        ];
        $giftcardAmounts->expects($this->any())->method('setValue')->with($this->equalTo($giftcardAmountsValue));
        $priceElement = $this->getMock(
            'Magento\Framework\Data\Form\Element\AbstractElement',
            ['setValue', 'setReadonly'],
            [],
            '',
            false
        );
        $priceElement->expects($this->any())->method('setValue')->with($this->equalTo('default'));
        $priceElement->expects($this->any())->method('setReadonly')->with(true, false);
        $map = [
            ['group-fields-advanced-pricing', $fieldsetGroup],
            ['price', $priceElement],
            ['giftcard_amounts', $giftcardAmounts],
        ];
        $form->expects($this->any())->method('getElement')->willReturnMap($map);
        $group = $this->getMock('\Magento\Framework\DataObject', ['getAttributeGroupCode'], [], '', false);
        $group->expects($this->any())->method('getAttributeGroupCode')->willReturn('advanced-pricing');
        $this->_block->expects($this->once())->method('getForm')->willReturn($form);
        $this->_block->expects($this->once())->method('getGroup')->willReturn($group);
        $this->_request->expects($this->once())->method('getParam')->with('store', 0)->willReturn(1);
        $store = $this->getMock(
            'Magento\Store\Model\Store',
            ['getWebsiteId', '__wakeup'],
            [],
            '',
            false
        );
        $store->expects($this->any())->method('getWebsiteId')->willReturn(1);
        $this->_storeManager->expects($this->any())->method('getStore')->with(1)->willReturn($store);
        $this->_observer->execute($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeCustomerCart()
    {
        $parentBlock = $this->getMock('Magento\Backend\Block\Template', ['getNameInLayout'], [], '', false);
        $parentBlock->expects(
            $this->once()
        )->method(
            'getNameInLayout'
        )->will(
            $this->returnValue('admin.customer.carts')
        );

        $this->_setGetNameInLayoutExpects('customer_cart_');
        $this->_block->expects($this->once())->method('getParentBlock')->willReturn($parentBlock);

        $this->_observer->expects(
            $this->exactly(2)
        )->method(
            '_removeColumnFromGrid'
        )->with(
            $this->isInstanceOf('Magento\Backend\Block\Widget\Grid'),
            $this->logicalOr($this->equalTo('price'), $this->equalTo('total'))
        );

        $this->_observer->execute($this->_varienObserver);
    }

    protected function _assertPriceColumnRemove()
    {
        $this->_observer->expects(
            $this->once()
        )->method(
            '_removeColumnFromGrid'
        )->with(
            $this->isInstanceOf('Magento\Backend\Block\Widget\Grid'),
            $this->equalTo('price')
        );
    }

    protected function _setGetNameInLayoutExpects($blockName)
    {
        $this->_block->expects($this->exactly(2))->method('getNameInLayout')->willReturn($blockName);
    }

    /**
     * @return array
     */
    public function productGridMassactionDataProvider()
    {
        return [['product.grid'], ['admin.product.grid']];
    }

    /**
     * @return array
     */
    public function gridCategoryProductGridDataProvider()
    {
        return [
            ['catalog.product.edit.tab.related'],
            ['catalog.product.edit.tab.upsell'],
            ['catalog.product.edit.tab.crosssell'],
            ['category.product.grid']
        ];
    }

    /*
     * @return array
     */
    public function checkoutAccordionDataProvider()
    {
        return [
            ['products'],
            ['wishlist'],
            ['compared'],
            ['rcompared'],
            ['rviewed'],
            ['ordered'],
            ['checkout.accordion.products'],
            ['checkout.accordion.wishlist'],
            ['checkout.accordion.compared'],
            ['checkout.accordion.rcompared'],
            ['checkout.accordion.rviewed'],
            ['checkout.accordion.ordered']
        ];
    }

    /**
     * @return array
     */
    public function checkoutItemsDataProvider()
    {
        return [['checkout.items'], ['items']];
    }
}
