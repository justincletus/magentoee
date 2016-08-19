<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Test\Unit\Model\Indexer\Plugin;

class GroupRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Indexer\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerMock;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appConfigMock;

    /**
     * @var \Magento\CatalogPermissions\Model\Indexer\Plugin\GroupRepository
     */
    protected $groupRepositoryPlugin;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerRegistryMock;

    protected function setUp()
    {
        $this->indexerMock = $this->getMock(
            'Magento\Indexer\Model\Indexer',
            ['getId', 'load', 'invalidate'],
            [],
            '',
            false
        );

        $this->appConfigMock = $this->getMock(
            'Magento\CatalogPermissions\App\Backend\Config',
            ['isEnabled'],
            [],
            '',
            false
        );

        $this->indexerRegistryMock = $this->getMock(
            'Magento\Framework\Indexer\IndexerRegistry',
            ['get'],
            [],
            '',
            false
        );

        $this->groupRepositoryPlugin = new \Magento\CatalogPermissions\Model\Indexer\Plugin\GroupRepository(
            $this->indexerRegistryMock,
            $this->appConfigMock
        );
    }

    public function testAfterDeleteGroupIndexerOff()
    {
        $customerGroupService = $this->getMock(
            'Magento\Customer\Model\ResourceModel\GroupRepository',
            [],
            [],
            '',
            false
        );
        $this->appConfigMock->expects($this->once())->method('isEnabled')->will($this->returnValue(false));
        $this->indexerRegistryMock->expects($this->never())->method('get');
        $this->groupRepositoryPlugin->afterDelete($customerGroupService);
    }

    public function testAfterDeleteIndexerOn()
    {
        $customerGroupService = $this->getMock(
            'Magento\Customer\Model\ResourceModel\GroupRepository',
            [],
            [],
            '',
            false
        );
        $this->appConfigMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));
        $this->indexerRegistryMock->expects($this->once())
            ->method('get')
            ->with(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID)
            ->will($this->returnValue($this->indexerMock));
        $this->indexerMock->expects($this->once())->method('invalidate');
        $this->groupRepositoryPlugin->afterDelete($customerGroupService);
    }

    public function testAfterSaveNoNeedInvalidating()
    {
        $customerGroupService = $this->getMock(
            'Magento\Customer\Model\ResourceModel\GroupRepository',
            [],
            [],
            '',
            false
        );

        $customerGroupMock = $this->getMock(
            'Magento\Customer\Model\Data\Group',
            ['getId'],
            [],
            '',
            false
        );
        $customerGroupMock->expects($this->once())->method('getId')->will($this->returnValue(10));
        $this->appConfigMock->expects($this->never())->method('isEnabled')->will($this->returnValue(true));

        $proceedMock = function () {
            return 10;
        };

        $this->groupRepositoryPlugin->aroundSave($customerGroupService, $proceedMock, $customerGroupMock);
    }

    public function testAfterSaveInvalidating()
    {
        $customerGroupService = $this->getMock(
            'Magento\Customer\Model\ResourceModel\GroupRepository',
            [],
            [],
            '',
            false
        );

        $customerGroupMock = $this->getMock(
            'Magento\Customer\Model\Data\Group',
            ['getId'],
            [],
            '',
            false
        );
        $customerGroupMock->expects($this->once())->method('getId')->will($this->returnValue(0));
        $this->appConfigMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));
        $this->indexerMock->expects($this->once())->method('invalidate');
        $this->indexerRegistryMock->expects($this->once())
            ->method('get')
            ->with(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID)
            ->will($this->returnValue($this->indexerMock));

        $proceedMock = function () {
            return 10;
        };

        $this->groupRepositoryPlugin->aroundSave($customerGroupService, $proceedMock, $customerGroupMock);
    }

    protected function prepareIndexer()
    {
        $this->indexerMock->expects($this->once())->method('getId')->will($this->returnValue(0));
        $this->indexerMock->expects(
            $this->once()
        )->method(
            'load'
        )->with(
            \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID
        )->will(
            $this->returnSelf()
        );
    }
}
