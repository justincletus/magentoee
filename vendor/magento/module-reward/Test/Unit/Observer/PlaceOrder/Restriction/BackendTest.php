<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Observer\PlaceOrder\Restriction;

class BackendTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Observer\PlaceOrder\Restriction\Backend
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_authorizationMock;

    protected function setUp()
    {
        $this->_helper = $this->getMock('Magento\Reward\Helper\Data', [], [], '', false);
        $this->_authorizationMock = $this->getMock('Magento\Framework\AuthorizationInterface');
        $this->_model = new \Magento\Reward\Observer\PlaceOrder\Restriction\Backend(
            $this->_helper,
            $this->_authorizationMock
        );
    }

    /**
     * @dataProvider testIsAllowedDataProvider
     * @param $expectedResult
     * @param $isEnabled
     * @param $isAllowed
     */
    public function testIsAllowed($expectedResult, $isEnabled, $isAllowed)
    {
        $this->_helper->expects($this->once())->method('isEnabledOnFront')->will($this->returnValue($isEnabled));
        $this->_authorizationMock->expects($this->any())->method('isAllowed')->will($this->returnValue($isAllowed));
        $this->assertEquals($expectedResult, $this->_model->isAllowed());
    }

    public function testIsAllowedDataProvider()
    {
        return [[true, true, true], [false, true, false], [false, false, false]];
    }
}
