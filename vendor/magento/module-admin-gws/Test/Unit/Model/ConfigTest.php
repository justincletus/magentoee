<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdminGws\Test\Unit\Model;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var \Magento\AdminGws\Model\Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock('Magento\AdminGws\Model\Config\Reader', [], [], '', false);
        $this->_configScopeMock = $this->getMock('Magento\Framework\Config\ScopeInterface');
        $this->_cacheMock = $this->getMock('Magento\Framework\Config\CacheInterface');
        $cacheId = null;

        $this->_model = new \Magento\AdminGws\Model\Config(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheMock,
            $cacheId
        );
    }

    /**
     * @dataProvider getCallbacksDataProvider
     */
    public function testGetCallbacks($value, $expected)
    {
        $this->_cacheMock->expects($this->any())->method('load')->will($this->returnValue(serialize($value)));

        $this->assertEquals($expected, $this->_model->getCallbacks('group'));
    }

    public function getCallbacksDataProvider()
    {
        return [
            'generic_key_exist' => [['callbacks' => ['group' => 'value']], 'value'],
            'return_default_value' => [['key_one' => 'value'], []]
        ];
    }

    /**
     * @dataProvider getDeniedAclResourcesDataProvider
     */
    public function testGetDeniedAclResources($value, $expected)
    {
        $this->_cacheMock->expects($this->any())->method('load')->will($this->returnValue(serialize($value)));

        $this->assertEquals($expected, $this->_model->getDeniedAclResources('level'));
    }

    public function getDeniedAclResourcesDataProvider()
    {
        return [
            'generic_key_exist' => [['acl' => ['level' => 'value']], 'value'],
            'return_default_value' => [['key_one' => 'value'], []]
        ];
    }
}
