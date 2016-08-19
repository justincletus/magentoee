<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Group\Configuration;

use Magento\Framework\App\Config;
use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractScopedConfigurationSectionTest extends AbstractConfigurationSectionTest
{
    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->configMock = $this->getMock('Magento\Framework\App\Config', [], [], '', false);
        $this->storeManagerMock = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->storeManagerMock->expects($this->any())->method('getStores')->willReturn([]);
    }

    /**
     * @return void
     */
    abstract public function testGetConfigDataItem();
}
