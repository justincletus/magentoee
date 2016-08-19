<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Test\Unit\Observer;

use Magento\CatalogPermissions\Observer\ApplyCategoryPermissionOnIsActiveFilterToCollectionObserver;
use Magento\Framework\DataObject;

/**
 * Test for \Magento\CatalogPermissions\Observer\ApplyCategoryPermissionOnIsActiveFilterToCollectionObserver
 */
class ApplyCategoryPermissionOnIsActiveFilterToCollectionObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogPermissions\Observer\ApplyCategoryPermissionOnIsActiveFilterToCollectionObserver
     */
    protected $observer;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $permissionsConfig;

    /**
     * @var \Magento\CatalogPermissions\Model\Permission\Index|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $permissionIndex;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Event\Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventObserverMock;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->storeManager = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->permissionsConfig = $this->getMock('Magento\CatalogPermissions\App\ConfigInterface');
        $this->permissionIndex = $this->getMock('Magento\CatalogPermissions\Model\Permission\Index', [], [], '', false);

        $this->eventObserverMock = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $this->observer =  new ApplyCategoryPermissionOnIsActiveFilterToCollectionObserver(
            $this->permissionsConfig,
            $this->storeManager,
            $this->getMock('Magento\Customer\Model\Session', [], [], '', false),
            $this->permissionIndex
        );
    }

    /**
     * @return void
     */
    public function testApplyCategoryPermissionOnIsActiveFilterToCollection()
    {
        $this->permissionsConfig
            ->expects($this->any())
            ->method('isEnabled')
            ->willReturn(true);
        $this->eventObserverMock
            ->expects($this->any())
            ->method('getEvent')
            ->willReturn(new DataObject(['category_collection' => 'Some Category Collection']));

        $this->permissionIndex
            ->expects($this->once())
            ->method('addIndexToCategoryCollection')
            ->with('Some Category Collection', $this->anything(), $this->anything());

        $this->storeManager
            ->expects($this->any())
            ->method('getStore')
            ->willReturn(new DataObject(['website_id' => 123]));

        $this->observer->execute($this->eventObserverMock);
    }
}
