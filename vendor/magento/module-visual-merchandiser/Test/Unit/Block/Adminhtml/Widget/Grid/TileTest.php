<?php
/***
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Test\Unit\Block\Adminhtml\Widget\Grid;

class TileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\VisualMerchandiser\Block\Adminhtml\Category\Tab\Merchandiser\Tile
     */
    protected $tileBlock;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collection;

    /**
     * Set up instances and mock objects
     */
    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $request->expects($this->atLeastOnce())->method('getParam')->will($this->returnValue(''));
        $request->expects($this->any())->method('has')->will($this->returnValue(false));

        $context = $this->getMock('Magento\Backend\Block\Template\Context', [], [], '', false);
        $context->expects($this->any())->method('getRequest')->will($this->returnValue($request));

        $collection = $this->getMock('Magento\Catalog\Model\ResourceModel\Product\Collection', [], [], '', false);
        $collection
            ->expects($this->any())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([]));
        $this->collection = $collection;

        $products = $this->getMock('Magento\VisualMerchandiser\Model\Category\Products', [], [], '', false);
        $products
            ->expects($this->atLeastOnce())
            ->method('getCollectionForGrid')
            ->willReturn($this->collection);
        $products
            ->expects($this->atLeastOnce())
            ->method('applyCachedChanges')
            ->willReturn($this->collection);

        $category = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false);
        $category
            ->expects($this->any())
            ->method('getProductsPosition')
            ->willReturn(['a' => 'b']);

        $coreRegistry = $this->getMock('Magento\Framework\Registry', [], [], '', false);
        $catalogImage = $this->getMock('Magento\Catalog\Helper\Image', [], [], '', false);
        $backendHelper = $this->getMock('Magento\Backend\Helper\Data', [], [], '', false);

        $this->tileBlock = $this->objectManager->getObject(
            'Magento\VisualMerchandiser\Block\Adminhtml\Category\Tab\Merchandiser\Tile',
            [
                'context' => $context,
                'backendHelper' => $backendHelper,
                'coreRegistry' => $coreRegistry,
                'catalogImage' => $catalogImage,
                'products' => $products
            ]
        );

        /** @var \Magento\Framework\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject $layout */
        $layout = $this->getMock('Magento\Framework\View\LayoutInterface');
        $layout
            ->expects($this->any())
            ->method('getParentName')
            ->willReturn('block');

        $block = $this->getMock('\Magento\Framework\DataObject', ['_getPositionCacheKey']);
        $layout
            ->expects($this->any())
            ->method('getBlock')
            ->willReturn($block);

        $this->tileBlock->setLayout($layout);

        $this->tileBlock->setPositionCacheKey('xxxxxx');
    }

    /**
     * Tests if collection is returned and set from _prepareCollection
     */
    public function testPrepareCollection()
    {
        $this->tileBlock->setData('id', 1);
        $collection = $this->tileBlock->getPreparedCollection();
        $this->assertEquals($this->collection, $this->tileBlock->getCollection());
        $this->assertEquals($this->collection, $collection);
    }
}
