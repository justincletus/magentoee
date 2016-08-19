<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogPermissions\Test\Unit\Model\Adminhtml;

use Magento\CatalogPermissions\Model\Adminhtml\AddPermissionTabOnCategoryEditPageObserver;
use Magento\Framework\DataObject;

/**
 * Unit test for Magento\CatalogPermissions\Model\Adminhtml\Observer
 */
class AddPermissionTabOnCategoryEditPageObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogPermissions\Model\Adminhtml\AddPermissionTabOnCategoryEditPageObserver
     */
    protected $observer;

    /**
     * @var \Magento\Framework\AuthorizationInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $authorizationMock;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->authorizationMock = $this->getMockBuilder('Magento\Framework\AuthorizationInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->configMock = $this->getMockBuilder('Magento\CatalogPermissions\App\ConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->observer = new AddPermissionTabOnCategoryEditPageObserver(
            $this->authorizationMock,
            $this->configMock
        );
    }

    /**
     * @param bool $isEnabled
     * @param bool $isAllowed
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $addTabsCall
     * @return void
     * @dataProvider addCategoryPermissionTabDataProvider
     */
    public function testAddCategoryPermissionTab($isEnabled, $isAllowed, $addTabsCall)
    {
        $this->configMock
            ->expects($this->atLeastOnce())
            ->method('isEnabled')
            ->willReturn($isEnabled);

        $this->authorizationMock
            ->expects($this->any())
            ->method('isAllowed')
            ->with('Magento_CatalogPermissions::catalog_magento_catalogpermissions', null)
            ->willReturn($isAllowed);

        $tabsMock = $this->getMockBuilder('Magento\Catalog\Block\Adminhtml\Category\Tabs')
            ->disableOriginalConstructor()
            ->getMock();

        $eventObserverMock = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();
        $eventObserverMock
            ->expects($this->any())
            ->method('getEvent')
            ->willReturn(new DataObject(['tabs' => $tabsMock]));

        $tabsMock
            ->expects($addTabsCall)
            ->method('addTab')
            ->with('permissions', 'Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions');

        $this->observer->execute($eventObserverMock);
    }

    /**
     * @return array
     */
    public function addCategoryPermissionTabDataProvider()
    {
        return [
            [false, false, $this->never()],
            [true, false, $this->never()],
            [false, true, $this->never()],
            [true, true, $this->once()]
        ];
    }
}
