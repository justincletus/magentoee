<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Test\Unit\Model\Indexer\Plugin;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConfigDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\CacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreCacheMock;

    /**
     * @var \Magento\Framework\Indexer\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerMock;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appConfigMock;

    /**
     * @var \Magento\Config\Model\Config\Loader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configLoaderMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Closure|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $closureMock;

    /**
     * @var \Magento\Config\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendConfigMock;

    /**
     * @var \Magento\CatalogPermissions\Model\Indexer\Plugin\ConfigData
     */
    protected $configData;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerRegistryMock;

    protected function setUp()
    {
        $this->coreCacheMock = $this->getMock('Magento\Framework\App\Cache', ['clean'], [], '', false);
        $this->appConfigMock = $this->getMock(
            'Magento\CatalogPermissions\App\Backend\Config',
            ['isEnabled'],
            [],
            '',
            false
        );
        $this->indexerMock = $this->getMock(
            'Magento\Indexer\Model\Indexer',
            ['getId', 'invalidate'],
            [],
            '',
            false
        );
        $this->configLoaderMock = $this->getMock(
            'Magento\Config\Model\Config\Loader',
            ['getConfigByPath'],
            [],
            '',
            false
        );
        $this->storeManagerMock = $this->getMock(
            'Magento\Store\Model\StoreManager',
            ['getStore', 'getWebsite'],
            [],
            '',
            false
        );
        $backendConfigMock = $this->backendConfigMock = $this->getMock(
            'Magento\Config\Model\Config',
            ['getStore', 'getWebsite', 'getSection'],
            [],
            '',
            false
        );
        $this->closureMock = function () use ($backendConfigMock) {
            return $backendConfigMock;
        };

        $this->indexerRegistryMock = $this->getMock(
            'Magento\Framework\Indexer\IndexerRegistry',
            ['get'],
            [],
            '',
            false
        );

        $this->configData = new \Magento\CatalogPermissions\Model\Indexer\Plugin\ConfigData(
            $this->coreCacheMock,
            $this->appConfigMock,
            $this->indexerRegistryMock,
            $this->configLoaderMock,
            $this->storeManagerMock
        );
    }

    public function testAroundSaveWithoutChanges()
    {
        $section = 'test';
        $this->backendConfigMock->expects($this->exactly(2))->method('getStore')->will($this->returnValue(false));
        $this->backendConfigMock->expects($this->exactly(2))->method('getWebsite')->will($this->returnValue(false));
        $this->backendConfigMock->expects($this->exactly(2))->method('getSection')->will($this->returnValue($section));
        $this->configLoaderMock->expects(
            $this->exactly(2)
        )->method(
            'getConfigByPath'
        )->with(
            $section . '/magento_catalogpermissions',
            'default',
            0,
            false
        )->will(
            $this->returnValue(['test' => 1])
        );
        $this->appConfigMock->expects($this->never())->method('isEnabled');

        $this->indexerRegistryMock->expects($this->never())->method('get');

        $this->configData->aroundSave($this->backendConfigMock, $this->closureMock);
    }

    public function testAroundSaveIndexerTurnedOff()
    {
        $section = 'test';
        $storeId = 5;

        $store = $this->getStore();
        $store->expects($this->exactly(2))->method('getId')->will($this->returnValue($storeId));
        $this->backendConfigMock->expects($this->exactly(4))->method('getStore')->will($this->returnValue($store));
        $this->storeManagerMock->expects($this->exactly(2))->method('getStore')->will($this->returnValue($store));

        $this->backendConfigMock->expects($this->never())->method('getWebsite');

        $this->backendConfigMock->expects($this->exactly(2))->method('getSection')->will($this->returnValue($section));
        $this->prepareConfigLoader($section, $storeId, 'stores');

        $this->appConfigMock->expects($this->once())->method('isEnabled')->will($this->returnValue(false));
        $this->coreCacheMock->expects($this->never())->method('clean');

        $this->configData->aroundSave($this->backendConfigMock, $this->closureMock);
    }

    public function testAroundSaveIndexerTurnedOn()
    {
        $section = 'test';
        $websiteId = 20;

        $store = $this->getStore();
        $store->expects($this->exactly(2))->method('getId')->will($this->returnValue($websiteId));
        $this->backendConfigMock->expects($this->exactly(4))->method('getWebsite')->will($this->returnValue($store));
        $this->storeManagerMock->expects($this->exactly(2))->method('getWebsite')->will($this->returnValue($store));

        $this->storeManagerMock->expects($this->never())->method('getStore');

        $this->backendConfigMock->expects($this->exactly(2))->method('getStore');

        $this->backendConfigMock->expects($this->exactly(2))->method('getSection')->will($this->returnValue($section));

        $this->prepareConfigLoader($section, $websiteId, 'websites');

        $this->appConfigMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));

        $this->coreCacheMock->expects(
            $this->once()
        )->method(
            'clean'
        )->with(
            [
                \Magento\Catalog\Model\Category::CACHE_TAG,
                \Magento\Framework\App\Cache\Type\Block::CACHE_TAG,
                \Magento\Framework\App\Cache\Type\Layout::CACHE_TAG
            ]
        );

        $this->indexerMock->expects($this->once())->method('invalidate');

        $this->indexerRegistryMock->expects($this->once())
            ->method('get')
            ->with(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID)
            ->will($this->returnValue($this->indexerMock));

        $this->configData->aroundSave($this->backendConfigMock, $this->closureMock);
    }

    /**
     * @return \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getStore()
    {
        $store = $this->getMock('Magento\Store\Model\Store', ['getId', '__wakeup'], [], '', false);
        return $store;
    }

    /**
     * @return \Magento\Store\Model\Website|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getWebsite()
    {
        $website = $this->getMock('Magento\Store\Model\Website', ['getId', '__wakeup'], [], '', false);
        return $website;
    }

    /**
     * @param string $section
     * @param int $objectId
     * @param string $type
     */
    protected function prepareConfigLoader($section, $objectId, $type)
    {
        $counter = 0;
        $this->configLoaderMock->expects(
            $this->exactly(2)
        )->method(
            'getConfigByPath'
        )->with(
            $section . '/magento_catalogpermissions',
            $type,
            $objectId,
            false
        )->will(
            $this->returnCallback(
                function () use (&$counter) {
                    return ++$counter % 2 ? ['test' => 1] : ['test' => 2];
                }
            )
        );
    }
}
