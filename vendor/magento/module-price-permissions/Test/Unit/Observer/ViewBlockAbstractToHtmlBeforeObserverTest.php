<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\PricePermissions\Test\Unit\Observer;

class ViewBlockAbstractToHtmlBeforeObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PricePermissions\Observer\ObserverData|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerData;

    /**
     * @covers \Magento\PricePermissions\Observer\ViewBlockAbstractToHtmlBeforeObserver::execute
     */
    public function testViewBlockAbstractToHtmlBefore()
    {
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(['isObjectNew', 'getIsRecurring', '__wakeup'])
            ->getMock();
        $product->expects($this->any())->method('isObjectNew')->will($this->returnValue(false));
        $product->expects($this->any())->method('getIsRecurring')->will($this->returnValue(true));

        $productFactory = $this->getMockBuilder('Magento\Catalog\Model\ProductFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $productFactory->expects($this->any())->method('create')->will($this->returnValue($product));

        $coreRegistry = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->setMethods(['registry'])
            ->getMock();
        $coreRegistry->expects($this->any())->method('registry')->with('product')->will($this->returnValue($product));

        $this->observerData = $this->getMock(
            'Magento\PricePermissions\Observer\ObserverData',
            [],
            [],
            '',
            false
        );
        $this->observerData->expects($this->any())->method('isCanEditProductPrice')->willReturn(false);
        $this->observerData->expects($this->any())->method('isCanReadProductPrice')->willReturn(false);

        $model = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))
            ->getObject('Magento\PricePermissions\Observer\ViewBlockAbstractToHtmlBeforeObserver',
                [
                    'coreRegistry' => $coreRegistry,
                    'productFactory' => $productFactory,
                    'observerData' => $this->observerData,
                ]
            );
        $block = $this->getMockBuilder(
            'Magento\Framework\View\Element\AbstractBlock'
        )->disableOriginalConstructor()->setMethods(
            [
                'getNameInLayout',
                'setProductEntity',
                'setIsReadonly',
                'addConfigOptions',
                'addFieldDependence',
                'setCanEditPrice'
            ]
        )->getMock();
        $observer = $this->getMockBuilder(
            'Magento\Framework\Event\Observer'
        )->disableOriginalConstructor()->setMethods(
            ['getBlock']
        )->getMock();
        $observer->expects($this->any())->method('getBlock')->will($this->returnValue($block));

        $nameInLayout = 'adminhtml.catalog.product.edit.tab.attributes';
        $block->expects($this->any())->method('getNameInLayout')->will($this->returnValue($nameInLayout));
        $block->expects($this->once())->method('setCanEditPrice')->with(false);

        $model->execute($observer);
    }
}
