<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GoogleTagManager\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Store\Model\ScopeInterface as Scope;
use Magento\GoogleTagManager\Helper\Data;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /** @var Data */
    protected $data;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $scopeConfig;

    protected function setUp()
    {
        $this->scopeConfig = $this->getMock(
            'Magento\Framework\App\Config\ScopeConfigInterface',
            [
                'getValue',
                'isSetFlag'
            ],
            [],
            '',
            false
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->data = $this->objectManagerHelper->getObject(
            'Magento\GoogleTagManager\Helper\Data',
            [
                'scopeConfig' => $this->scopeConfig
            ]
        );
    }

    /**
     * @param string $storeCode
     * @param string $gaAccount
     * @param string $gtmAccount
     * @param string $type
     * @param bool $active
     * @param bool $expected
     *
     * @dataProvider isGoogleAnalyticsAvailableDataProvider
     */
    public function testIsGoogleAnalyticsAvailable($storeCode, $gaAccount, $gtmAccount, $type, $active, $expected)
    {
        $this->scopeConfig->expects($this->any())->method('getValue')->willReturnMap([
            [Data::XML_PATH_ACCOUNT, Scope::SCOPE_STORE, $storeCode, $gaAccount],
            [Data::XML_PATH_CONTAINER_ID, Scope::SCOPE_STORE, $storeCode, $gtmAccount],
            [Data::XML_PATH_TYPE, Scope::SCOPE_STORE, $storeCode, $type],
        ]);
        $this->scopeConfig->expects($this->any())->method('isSetFlag')->willReturnMap([
            [Data::XML_PATH_ACTIVE, Scope::SCOPE_STORE, $storeCode, $active]
        ]);
        $this->assertEquals($expected, $this->data->isGoogleAnalyticsAvailable($storeCode));
    }

    public function isGoogleAnalyticsAvailableDataProvider()
    {
        return [
            [null, 'GAAccountId', 'GTMAccountId', Data::TYPE_UNIVERSAL, true, true],
            [null, 'GAAccountId', null, Data::TYPE_UNIVERSAL, true, true],
            [null, null, 'GTMAccountId', Data::TYPE_UNIVERSAL, true, false],
            [null, null, null, Data::TYPE_UNIVERSAL, true, false],
            [null, 'GAAccountId', 'GTMAccountId', Data::TYPE_TAG_MANAGER, true, true],
            [null, 'GAAccountId', null, Data::TYPE_TAG_MANAGER, true, false],
            [null, null, 'GTMAccountId', Data::TYPE_TAG_MANAGER, true, true],
            [null, null, null, Data::TYPE_TAG_MANAGER, true, false],
            [null, 'GAAccountId', 'GTMAccountId', Data::TYPE_TAG_MANAGER, false, false],
            ['store1', 'GAAccountId', 'GTMAccountId', Data::TYPE_TAG_MANAGER, true, true],
        ];
    }

    /**
     * @param string $storeCode
     * @param string $gtmAccount
     * @param string $type
     * @param bool $active
     * @param bool $expected
     *
     * @dataProvider isTagManagerAvailableDataProvider
     */
    public function testIsTagManagerAvailable($storeCode, $gtmAccount, $type, $active, $expected)
    {
        $this->scopeConfig->expects($this->any())->method('getValue')->willReturnMap([
            [Data::XML_PATH_CONTAINER_ID, Scope::SCOPE_STORE, $storeCode, $gtmAccount],
            [Data::XML_PATH_TYPE, Scope::SCOPE_STORE, $storeCode, $type],
        ]);
        $this->scopeConfig->expects($this->any())->method('isSetFlag')->willReturnMap([
            [Data::XML_PATH_ACTIVE, Scope::SCOPE_STORE, $storeCode, $active]
        ]);
        $this->assertEquals($expected, $this->data->isTagManagerAvailable($storeCode));
    }

    public function isTagManagerAvailableDataProvider()
    {
        return [
            [null, 'GTMAccountId', Data::TYPE_TAG_MANAGER, true, true],
            [null, null, Data::TYPE_TAG_MANAGER, true, false],
            [null, 'GTMAccountId', Data::TYPE_TAG_MANAGER, false, false],
            [null, 'GTMAccountId', Data::TYPE_UNIVERSAL, true, false],
            [null, null, Data::TYPE_UNIVERSAL, false, false],
            ['store1', 'GTMAccountId', Data::TYPE_TAG_MANAGER, true, true],
        ];
    }
}
