<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogEvent\Test\Unit\Block\Event;

use Magento\CatalogEvent\Block\Event\Lister;

/**
 * Unit test for Magento\CatalogEvent\Block\Event\Lister
 */
class ListerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogEvent\Block\Event\Lister
     */
    protected $lister;

    /**
     * @var \Magento\Framework\View\Element\Template\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolverMock;

    /**
     * @var \Magento\CatalogEvent\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogEventHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactoryMock;

    /**
     * @var \Magento\Catalog\Helper\Category|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogCategoryHelperMock;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->contextMock = $this->getMockBuilder('Magento\Framework\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resolverMock = $this->getMockBuilder('Magento\Framework\Locale\ResolverInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->catalogEventHelperMock = $this->getMockBuilder('Magento\CatalogEvent\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->collectionFactoryMock = $this->getMockBuilder(
            'Magento\CatalogEvent\Model\ResourceModel\Event\CollectionFactory'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->catalogCategoryHelperMock = $this->getMockBuilder('Magento\Catalog\Helper\Category')
            ->disableOriginalConstructor()
            ->getMock();

        $this->lister = new Lister(
            $this->contextMock,
            $this->resolverMock,
            $this->catalogEventHelperMock,
            $this->collectionFactoryMock,
            $this->catalogCategoryHelperMock
        );
    }


    /**
     * @return void
     */
    public function testGetCategoryUrl()
    {
        $parameterMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false);

        $this->catalogCategoryHelperMock
            ->expects($this->once())
            ->method('getCategoryUrl')
            ->with($parameterMock)
            ->willReturn('Result');

        $this->assertEquals('Result', $this->lister->getCategoryUrl($parameterMock));
    }

    /**
     * @return void
     */
    public function testGetEventImageUrl()
    {
        $eventMock = $this->getMock('Magento\CatalogEvent\Model\Event', [], [], '', false);
        $this->catalogEventHelperMock
            ->expects($this->once())
            ->method('getEventImageUrl')
            ->with($eventMock)
            ->willReturn('Result');

        $this->assertEquals('Result', $this->lister->getEventImageUrl($eventMock));
    }
}
