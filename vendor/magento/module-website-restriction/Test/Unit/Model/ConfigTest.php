<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\WebsiteRestriction\Test\Unit\Model;

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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopeConfigMock;

    /**
     * @var \Magento\WebsiteRestriction\Model\Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock(
            'Magento\WebsiteRestriction\Model\Config\Reader',
            [],
            [],
            '',
            false
        );
        $this->_configScopeMock = $this->getMock('Magento\Framework\Config\ScopeInterface');
        $this->_cacheMock = $this->getMock('Magento\Framework\Config\CacheInterface');
        $this->_scopeConfigMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $cacheId = null;

        $this->_model = new \Magento\WebsiteRestriction\Model\Config(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheMock,
            $this->_scopeConfigMock,
            $cacheId
        );
    }

    /**
     * @dataProvider getGenericActionsDataProvider
     */
    public function testGetGenericActions($value, $expected)
    {
        $this->_cacheMock->expects($this->any())->method('load')->will($this->returnValue(serialize($value)));

        $this->assertEquals($expected, $this->_model->getGenericActions());
    }

    public function getGenericActionsDataProvider()
    {
        return [
            'generic_key_exist' => [['generic' => 'value'], 'value'],
            'return_default_value' => [['key_one' => 'value'], []]
        ];
    }

    /**
     * @dataProvider getRegisterActionsDataProvider
     */
    public function testGetRegisterActions($value, $expected)
    {
        $this->_cacheMock->expects($this->any())->method('load')->will($this->returnValue(serialize($value)));

        $this->assertEquals($expected, $this->_model->getRegisterActions());
    }

    public function getRegisterActionsDataProvider()
    {
        return [
            'register_key_exist' => [['register' => 'value'], 'value'],
            'return_default_value' => [['key_one' => 'value'], []]
        ];
    }

    public function testIsRestrictionEnabled()
    {
        $store = null;
        $this->_scopeConfigMock->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            'general/restriction/is_active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        )->will(
            $this->returnValue(false)
        );

        $this->assertEquals(false, $this->_model->isRestrictionEnabled($store));
    }

    public function testGetMode()
    {
        $this->_scopeConfigMock->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            'general/restriction/mode',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )->will(
            $this->returnValue(false)
        );
        $this->assertEquals(0, $this->_model->getMode());
    }

    public function testGetHTTPStatusCode()
    {
        $this->_scopeConfigMock->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            'general/restriction/http_status'
        )->will(
            $this->returnValue(false)
        );
        $this->assertEquals(0, $this->_model->getHTTPStatusCode());
    }

    public function testGetHTTPRedirectCode()
    {
        $this->_scopeConfigMock->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            'general/restriction/http_redirect',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )->will(
            $this->returnValue(true)
        );
        $this->assertEquals(1, $this->_model->getHTTPRedirectCode());
    }

    public function testGetLandingPageCode()
    {
        $this->_scopeConfigMock->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            'general/restriction/cms_page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )->will(
            $this->returnValue('config')
        );
        $this->assertEquals('config', $this->_model->getLandingPageCode());
    }
}
