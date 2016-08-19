<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogEvent\Test\Unit\Block\Adminhtml\Event\Edit;

use Magento\CatalogEvent\Block\Adminhtml\Event\Edit\Category;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Unit test for Magento\CatalogEvent\Block\Adminhtml\Event\Edit\Category
 */
class CategoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogEvent\Block\Adminhtml\Event\Edit\Category
     */
    protected $category;

    /**
     * @var \Magento\Backend\Block\Template\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Tree|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $treeMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryFactoryMock;

    /**
     * @var \Magento\Framework\Json\EncoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $encoderMock;

    /**
     * @var \Magento\CatalogEvent\Helper\Adminhtml\Event|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogEventAdminhtmlHelperMock;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->contextMock = (new ObjectManager($this))->getObject('Magento\Backend\Block\Template\Context');
        $this->treeMock = $this->getMockBuilder('Magento\Catalog\Model\ResourceModel\Category\Tree')
            ->disableOriginalConstructor()
            ->getMock();
        $this->registryMock = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryFactoryMock = $this->getMockBuilder('Magento\Catalog\Model\CategoryFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->encoderMock = $this->getMockBuilder('Magento\Framework\Json\EncoderInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->catalogEventAdminhtmlHelperMock = $this->getMockBuilder('Magento\CatalogEvent\Helper\Adminhtml\Event')
            ->disableOriginalConstructor()
            ->getMock();

        $this->category = new Category(
            $this->contextMock,
            $this->treeMock,
            $this->registryMock,
            $this->categoryFactoryMock,
            $this->encoderMock,
            $this->catalogEventAdminhtmlHelperMock
        );
    }

    /**
     * @return void
     */
    public function testGetLoadTreeUrl()
    {
        $this->contextMock
            ->getUrlBuilder()
            ->expects($this->once())
            ->method('getUrl')
            ->with('adminhtml/*/categoriesJson', [])
            ->willReturn('result');

        $this->assertEquals(
            'result',
            $this->category->getLoadTreeUrl()
        );
    }
}
