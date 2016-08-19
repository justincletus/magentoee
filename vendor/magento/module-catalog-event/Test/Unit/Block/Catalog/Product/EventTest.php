<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogEvent\Test\Unit\Block\Catalog\Product;

class EventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogEvent\Block\Catalog\Product\Event
     */
    protected $block;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->registryMock = $this->getMock('Magento\Framework\Registry', [], [], '', false);

        $this->block = $objectManager->getObject(
            'Magento\CatalogEvent\Block\Catalog\Product\Event',
            ['registry' => $this->registryMock]
        );
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $productTags = ['catalog_product_1'];
        $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $product->expects($this->once())->method('getIdentities')->will($this->returnValue($productTags));
        $this->registryMock->expects(
            $this->once()
        )->method(
            'registry'
        )->with(
            'current_product'
        )->will(
            $this->returnValue($product)
        );
        $this->assertEquals($productTags, $this->block->getIdentities());
    }
}
