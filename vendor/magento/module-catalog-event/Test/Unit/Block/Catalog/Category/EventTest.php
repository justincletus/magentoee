<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogEvent\Test\Unit\Block\Catalog\Category;

use Magento\Framework\DataObject;

class EventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogEvent\Block\Catalog\Category\Event
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
            'Magento\CatalogEvent\Block\Catalog\Category\Event',
            ['registry' => $this->registryMock]
        );
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $categoryTags = ['catalog_category_1'];
        $category = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false);
        $category->expects($this->once())->method('getIdentities')->will($this->returnValue($categoryTags));
        $this->registryMock->expects(
            $this->once()
        )->method(
            'registry'
        )->with(
            'current_category'
        )->will(
            $this->returnValue($category)
        );
        $this->assertEquals($categoryTags, $this->block->getIdentities());
    }

    public function testGetEvent()
    {
        $this->registryMock
            ->expects($this->once())
            ->method('registry')
            ->with('current_category')
            ->willReturn(new DataObject(['event' => 'some result']));

        $this->assertEquals('some result', $this->block->getEvent());
    }
}
