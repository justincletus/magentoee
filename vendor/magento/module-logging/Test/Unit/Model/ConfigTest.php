<?php
/**
 * Test \Magento\Logging\Model\Config
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Logging\Test\Unit\Model;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Logging\Model\Config\Data
     */
    protected $_storageMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Logging\Model\Config
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopeConfigMock;

    public function setUp()
    {
        $this->_storageMock = $this->getMockBuilder(
            'Magento\Logging\Model\Config\Data'
        )->setMethods(
            ['get']
        )->disableOriginalConstructor()->getMock();

        $loggingConfig = [
            'actions' => [
                'test_action_withlabel' => ['label' => 'Test Action Label'],
                'test_action_withoutlabel' => [],
            ],
            'test' => ['label' => 'Test Label'],
            'configured_log_group' => [
                'label' => 'Log Group With Configuration',
                'actions' => [
                    'adminhtml_checkout_index' => [
                        'log_name' => 'configured_log_group',
                        'action' => 'view',
                        'expected_models' => ['Magento\Framework\Model\AbstractModel' => []],
                    ],
                ],
            ],
        ];
        $this->_storageMock->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            $this->equalTo('logging')
        )->will(
            $this->returnValue($loggingConfig)
        );

        $this->_scopeConfigMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');

        $this->_model = new \Magento\Logging\Model\Config($this->_storageMock, $this->_scopeConfigMock);
    }

    public function testLabels()
    {
        $expected = ['test' => 'Test Label', 'configured_log_group' => 'Log Group With Configuration'];
        $result = $this->_model->getLabels();
        $this->assertEquals($expected, $result);
    }

    public function testGetActionLabel()
    {
        $expected = 'Test Action Label';
        $result = $this->_model->getActionLabel('test_action_withlabel');
        $this->assertEquals($expected, $result);
    }

    public function testGetActionWithoutLabel()
    {
        $this->assertEquals('test_action_withoutlabel', $this->_model->getActionLabel('test_action_withoutlabel'));
        $this->assertEquals('nonconfigured_action', $this->_model->getActionLabel('nonconfigured_action'));
    }

    public function testGetSystemConfigValues()
    {
        $config = ['enterprise_checkout' => 1, 'customer' => 1];
        $this->_scopeConfigMock->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            $this->equalTo('admin/magento_logging/actions'), \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )->will(
            $this->returnValue(serialize($config))
        );
        $this->assertEquals($config, $this->_model->getSystemConfigValues());
    }

    public function testGetSystemConfigValuesNegative()
    {
        $expected = ['test' => 1, 'configured_log_group' => 1];
        $this->_scopeConfigMock->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            $this->equalTo('admin/magento_logging/actions'), \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )->will(
            $this->returnValue(null)
        );
        $this->assertEquals($expected, $this->_model->getSystemConfigValues());
    }

    public function testHasSystemConfigValues()
    {
        $config = ['enterprise_checkout' => 1, 'customer' => 1];

        $this->_scopeConfigMock->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            $this->equalTo('admin/magento_logging/actions'), \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )->will(
            $this->returnValue(serialize($config))
        );

        $this->assertTrue($this->_model->hasSystemConfigValue('enterprise_checkout'));
        $this->assertFalse($this->_model->hasSystemConfigValue('enterprise_catalogevent'));
    }

    public function testIsEventGroupLogged()
    {
        $config = ['enterprise_checkout' => 1, 'customer' => 1];

        $this->_scopeConfigMock->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            $this->equalTo('admin/magento_logging/actions'), \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )->will(
            $this->returnValue(serialize($config))
        );

        $this->assertTrue($this->_model->isEventGroupLogged('enterprise_checkout'));
        $this->assertFalse($this->_model->isEventGroupLogged('enterprise_catalogevent'));
    }

    public function testGetEventByFullActionName()
    {
        $expected = [
            'log_name' => 'configured_log_group',
            'action' => 'view',
            'expected_models' => ['Magento\Framework\Model\AbstractModel' => []],
        ];
        $this->assertEquals($expected, $this->_model->getEventByFullActionName('adminhtml_checkout_index'));
    }
}
