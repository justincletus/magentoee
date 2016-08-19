<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Block\Checkout\Cart;

use ArrayIterator;
use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CrosssellTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\TargetRule\Block\Checkout\Cart\Crosssell */
    protected $crosssell;

    /** @var \Magento\TargetRule\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $targetRuleHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $linkFactory;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;
    /**
     * @var \Magento\Checkout\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $checkoutSession;
    /**
     * @var \Magento\TargetRule\Model\IndexFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactory;
    /**
     * @var \Magento\TargetRule\Model\ResourceModel\Index|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $index;

    protected function setUp()
    {
        $this->checkoutSession =
            $this->getMock('Magento\Checkout\Model\Session', ['getQuote', 'getLastAddedProductId'], [], '', false);
        $this->indexFactory =
            $this->getMock('Magento\TargetRule\Model\IndexFactory', ['create'], [], '', false);
        $this->collectionFactory =
            $this->getMock('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory', ['create'], [], '', false);
        $this->index = $this->getMock('Magento\TargetRule\Model\ResourceModel\Index', [], [], '', false);

        $this->storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $catalogConfig = $this->getMock('Magento\Catalog\Model\Config', [], [], '', false);
        $context = $this->getMock('Magento\Catalog\Block\Product\Context', [], [], '', false);
        $context->expects($this->any())->method('getStoreManager')->willReturn($this->storeManager);
        $context->expects($this->any())->method('getCatalogConfig')->willReturn($catalogConfig);
        $this->targetRuleHelper = $this->getMock('Magento\TargetRule\Helper\Data', [], [], '', false);
        $visibility = $this->getMock('Magento\Catalog\Model\Product\Visibility', [], [], '', false);
        $status = $this->getMock('Magento\CatalogInventory\Model\Stock\Status', [], [], '', false);
        $this->linkFactory = $this->getMock('Magento\Catalog\Model\Product\LinkFactory', ['create'], [], '', false);
        $productFactory = $this->getMock('Magento\Catalog\Model\ProductFactory', ['create'], [], '', false);
        $config = $this->getMock('Magento\Catalog\Model\ProductTypes\ConfigInterface');

        $this->crosssell = (new ObjectManager($this))->getObject(
            'Magento\TargetRule\Block\Checkout\Cart\Crosssell',
            [
                'context' => $context,
                'index' => $this->index,
                'targetRuleData' => $this->targetRuleHelper,
                'productCollectionFactory' => $this->collectionFactory,
                'visibility' => $visibility,
                'status' => $status,
                'session' => $this->checkoutSession,
                'productLinkFactory' => $this->linkFactory,
                'productFactory' => $productFactory,
                'indexFactory' => $this->indexFactory,
                'productTypeConfig' => $config
            ]
        );
    }

    /**
     * @covers Magento\TargetRule\Block\Checkout\Cart\Crosssell::_getTargetLinkCollection
     */
    public function testGetTargetLinkCollection()
    {
        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $this->storeManager->expects($this->any())->method('getStore')->willReturn($store);
        $this->targetRuleHelper->expects($this->once())->method('getMaximumNumberOfProduct')
            ->with(\Magento\TargetRule\Model\Rule::CROSS_SELLS);
        $productCollection = $this->getMock(
            'Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection',
            [],
            [],
            '',
            false
        );
        $productLinkCollection = $this->getMock('Magento\Catalog\Model\Product\Link', [], [], '', false);
        $this->linkFactory->expects($this->once())->method('create')->willReturn($productLinkCollection);
        $productLinkCollection->expects($this->once())->method('useCrossSellLinks')->willReturnSelf();
        $productLinkCollection->expects($this->once())->method('getProductCollection')->willReturn($productCollection);
        $productCollection->expects($this->once())->method('setStoreId')->willReturnSelf();
        $productCollection->expects($this->once())->method('setPageSize')->willReturnSelf();
        $productCollection->expects($this->once())->method('setGroupBy')->willReturnSelf();
        $productCollection->expects($this->once())->method('addMinimalPrice')->willReturnSelf();
        $productCollection->expects($this->once())->method('addFinalPrice')->willReturnSelf();
        $productCollection->expects($this->once())->method('addTaxPercents')->willReturnSelf();
        $productCollection->expects($this->once())->method('addAttributeToSelect')->willReturnSelf();
        $productCollection->expects($this->once())->method('addUrlRewrite')->willReturnSelf();
        $select = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);
        $productCollection->expects($this->once())->method('getSelect')->willReturn($select);

        $this->assertSame($productCollection, $this->crosssell->getLinkCollection());
    }

    /**
     * @param int $limit
     * @param int $numberOfCrossSells
     * @param int $linkProducts
     * @param int $expected
     * @dataProvider getItemCollectionDataProvider
     */
    public function testGetItemCollection($limit, $numberOfCrossSells, $linkProducts, $expected)
    {
        $this->storeManager->method('getStore')->willReturn(new DataObject(['id' => 1]));

        $items = [
            new DataObject(['product' => new DataObject(['entity_id' => 999])])
        ];
        $quote = new DataObject(['all_items' => $items]);

        $this->checkoutSession->method('getQuote')->willReturn($quote);
        $this->checkoutSession->method('getLastAddedProductId')->willReturn(1);

        $targetRuleIndex = $this->getMockBuilder('\Magento\TargetRule\Model\Index')
            ->disableOriginalConstructor()
            ->setMethods(['setType', 'setLimit', 'setProduct', 'setExcludeProductIds', 'getProductIds'])
            ->getMock();
        $targetRuleIndex->method('setType')->will($this->returnSelf());
        $targetRuleIndex->method('setLimit')->will($this->returnSelf());
        $targetRuleIndex->method('setProduct')->will($this->returnSelf());
        $targetRuleIndex->method('setExcludeProductIds')->will($this->returnSelf());
        $targetRuleIndex->method('getProductIds')->willReturn([999]);

        $linkCollection = $this->_getLinkCollection($linkProducts);
        $this->linkFactory->method('create')->willReturn($linkCollection);

        $this->indexFactory->method('create')->willReturn($targetRuleIndex);

        $this->collectionFactory->method('create')->willReturn($this->_getProductCollection($numberOfCrossSells));

        $this->targetRuleHelper
            ->method('getMaximumNumberOfProduct')
            ->with(\Magento\TargetRule\Model\Rule::CROSS_SELLS)
            ->willReturn($limit);

        $this->assertCount($expected, $this->crosssell->getItemCollection());
    }

    public function getItemCollectionDataProvider()
    {
        return [
            [5, 6, 0, 5],
            [5, 4, 0, 4],
            [5, 4, 6, 5],
            [0, 4, 6, 0]
        ];
    }

    /**
     * @param int $numberOfProducts
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getProductCollection($numberOfProducts)
    {
        $productCollection = $this->getMock(
            'Magento\Catalog\Model\ResourceModel\Product\Collection',
            [
                'addMinimalPrice',
                'addFinalPrice',
                'addTaxPercents',
                'addAttributeToSelect',
                'addUrlRewrite',
                'getStoreId',
                'addFieldToFilter',
                'isEnabledFlat',
                'setVisibility',
                'getIterator'
            ],
            [],
            '',
            false
        );
        $productCollection->method('addMinimalPrice')->will($this->returnSelf());
        $productCollection->method('addFinalPrice')->will($this->returnSelf());
        $productCollection->method('addTaxPercents')->will($this->returnSelf());
        $productCollection->method('addAttributeToSelect')->will($this->returnSelf());
        $productCollection->method('addUrlRewrite')->will($this->returnSelf());
        $productCollection->method('addFieldToFilter')->will($this->returnSelf());
        $productCollection->method('setVisibility')->will($this->returnSelf());
        $productCollection->method('getStoreId')->willReturn(1);
        $productCollection->method('isEnabledFlat')->willReturn(false);

        $productCollection->method('getIterator')->willReturn($this->_getProducts(101, $numberOfProducts));

        return $productCollection;
    }

    /**
     * @param int $numberOfProducts
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getLinkCollection($numberOfProducts)
    {
        $linkCollection = $this->getMock(
            '\Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection',
            [
                'useCrossSellLinks',
                'getProductCollection',
                'setStoreId',
                'setPageSize',
                'setGroupBy',
                'setVisibility',
                'addMinimalPrice',
                'addFinalPrice',
                'addTaxPercents',
                'addAttributeToSelect',
                'addUrlRewrite',
                'getSelect',
                'getIterator'
            ],
            [],
            '',
            false
        );

        $linkCollection->method('useCrossSellLinks')->will($this->returnSelf());
        $linkCollection->method('getProductCollection')->will($this->returnSelf());
        $linkCollection->method('setStoreId')->will($this->returnSelf());
        $linkCollection->method('setPageSize')->will($this->returnSelf());
        $linkCollection->method('setGroupBy')->will($this->returnSelf());
        $linkCollection->method('setVisibility')->will($this->returnSelf());
        $linkCollection->method('addMinimalPrice')->will($this->returnSelf());
        $linkCollection->method('addFinalPrice')->will($this->returnSelf());
        $linkCollection->method('addTaxPercents')->will($this->returnSelf());
        $linkCollection->method('addAttributeToSelect')->will($this->returnSelf());
        $linkCollection->method('addUrlRewrite')->will($this->returnSelf());
        $linkCollection->method('getSelect')->willReturn(
            $this->getMock('Magento\Framework\DB\Select', [], [], '', false)
        );
        $linkCollection->method('getIterator')->willReturn($this->_getProducts(201, $numberOfProducts));
        return $linkCollection;
    }

    /**
     * @param int $startId
     * @param int $numberOfProducts
     * @return ArrayIterator
     */
    protected function _getProducts($startId, $numberOfProducts)
    {
        $items = [];
        for ($i = 0; $i < $numberOfProducts; $i++) {
            $items[] = new DataObject(['entity_id' => $startId + $i]);
        }
        return new ArrayIterator($items);

    }
}
