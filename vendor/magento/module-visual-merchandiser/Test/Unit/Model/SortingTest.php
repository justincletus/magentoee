<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class SortingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $category;

    /**
     * @var \Magento\VisualMerchandiser\Model\Sorting\Factory
     */
    protected $sortingFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $collection;
    /**
     * @var \Magento\VisualMerchandiser\Model\Sorting
     */
    protected $model;

    /**
     * @var \Magento\VisualMerchandiser\Model\Sorting\UserDefined
     */
    protected $sorting;

    /**
     * Set up instances and mock objects
     */
    protected function setUp()
    {
        $this->sortingFactory = $this->getMockBuilder('\Magento\VisualMerchandiser\Model\Sorting\Factory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $userSort = $this->getMockBuilder('\Magento\VisualMerchandiser\Model\Sorting\UserDefined')
            ->disableOriginalConstructor()
            ->getMock();

        $this->sortingFactory
            ->expects($this->any())
            ->method('create')
            ->with(
                $this->logicalOr(
                    'UserDefined',
                    'LowStockTop',
                    'OutStockBottom',
                    'SpecialPriceTop',
                    'SpecialPriceBottom',
                    'NewestTop',
                    'SortColor',
                    'Name\Ascending',
                    'Name\Descending',
                    'Sku\Ascending',
                    'Sku\Descending',
                    'Price\HighToLow',
                    'Price\LowToHigh'
                )
            )
            ->willReturn($userSort);

        $this->category = $this->getMockBuilder('\Magento\Catalog\Model\Category')
            ->disableOriginalConstructor()
            ->getMock();

        $this->category
            ->expects($this->any())
            ->method('getAutomaticSorting')
            ->willReturn(1);

        $this->collection = $this->getMockBuilder('\Magento\Catalog\Model\ResourceModel\Product\Collection')
            ->disableOriginalConstructor()
            ->getMock();

        $userSort
            ->expects($this->any())
            ->method('sort')
            ->willReturn($this->collection);

        $this->collection
            ->expects($this->any())
            ->method('isLoaded')
            ->will($this->returnValue(false));

        $this->model = (new ObjectManager($this))->getObject(
            '\Magento\VisualMerchandiser\Model\Sorting',
            [
                'factory' => $this->sortingFactory
            ]
        );
    }

    /**
     * Tests the method getSortingOptions
     */
    public function testGetSortingOptions()
    {
        $this->assertInternalType('array', $this->model->getSortingOptions());
    }

    /**
     * Tests the method getSortingInstance
     */
    public function testGetSortingInstance()
    {
        $this->assertInstanceOf(
            '\Magento\VisualMerchandiser\Model\Sorting\UserDefined',
            $this->model->getSortingInstance(null)
        );
    }

    /**
     * Tests the method applySorting
     */
    public function testApplySorting()
    {
        $this->assertInstanceOf(
            '\Magento\Catalog\Model\ResourceModel\Product\Collection',
            $this->model->applySorting(
                $this->category,
                $this->collection
            )
        );
    }
}
