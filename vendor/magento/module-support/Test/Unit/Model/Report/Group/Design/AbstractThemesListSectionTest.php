<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Group\Design;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

abstract class AbstractThemesListSectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $themeCollectionFactoryMock;

    /**
     * @var \Magento\Theme\Model\ResourceModel\Theme\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $themeCollectionMock;

    /**
     * @var \Magento\Theme\Model\Theme|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $themeMock;

    /**
     * @var \Magento\Theme\Model\Theme|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $parentThemeMock;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->themeCollectionMock = $this->getMockBuilder('Magento\Theme\Model\ResourceModel\Theme\Collection')
            ->disableOriginalConstructor()
            ->setMethods(['load', 'addFieldToFilter', 'setOrder', 'getItems'])
            ->getMock();
        $this->themeCollectionFactoryMock = $this->getMockBuilder(
            'Magento\Theme\Model\ResourceModel\Theme\CollectionFactory'
        )->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->themeCollectionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->themeCollectionMock);
        $this->themeCollectionMock->expects($this->any())->method('addFieldToFilter')->willReturnSelf();
        $this->themeCollectionMock->expects($this->once())->method('setOrder')->willReturnSelf();
        $this->themeCollectionMock->expects($this->once())->method('load')->willReturnSelf();

        $this->parentThemeMock = $this->getMockBuilder('Magento\Theme\Model\Theme')
            ->setMethods(['getThemePath'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Create theme model mock
     *
     * @param string $themePath
     * @param \Magento\Theme\Model\Theme|\PHPUnit_Framework_MockObject_MockObject|null $parentThemeMock
     * @param string|null $parentThemePath
     * @return \Magento\Theme\Model\Theme|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getThemeMock($themePath, $parentThemeMock = null, $parentThemePath = null)
    {
        $themeMock = $this->getMock('Magento\Theme\Model\Theme', ['getParentTheme', 'getThemePath'], [], '', false);
        $themeMock->expects($this->atLeastOnce())->method('getParentTheme')->willReturn($parentThemeMock);
        $this->parentThemeMock->expects($this->any())->method('getThemePath')->willReturn($parentThemePath);
        $themeMock->expects($this->once())->method('getThemePath')->willReturn($themePath);

        return $themeMock;
    }
}
