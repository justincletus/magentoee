<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PricePermissions\Test\Unit\Controller\Adminhtml\Product\Initialization\Helper\Plugin;

class PricePermissionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PricePermissions
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $authSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pricePermDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productHandlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->authSessionMock = $this->getMock(
            '\Magento\Backend\Model\Auth\Session',
            ['isLoggedIn', 'getUser'],
            [],
            '',
            false
        );
        $this->pricePermDataMock = $this->getMock(
            '\Magento\PricePermissions\Helper\Data',
            [],
            [],
            '',
            false
        );
        $this->productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $this->productHandlerMock = $this->getMock(
            '\Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper\HandlerInterface'
        );
        $this->userMock = $this->getMock('\Magento\User\Model\User', [], [], '', false);

        $this->subjectMock = $this->getMock(
            'Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper',
            [],
            [],
            '',
            false
        );
        $this->_model = new \Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\PricePermissions(
            $this->authSessionMock,
            $this->pricePermDataMock,
            $this->productHandlerMock
        );
    }

    public function testAfterInitializeWithNotLoggedInUser()
    {
        $this->authSessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue(false));
        $this->pricePermDataMock->expects($this->never())->method('getCanAdminEditProductPrice');

        $this->productHandlerMock->expects($this->once())->method('handle')->with($this->productMock);

        $this->assertEquals(
            $this->productMock,
            $this->_model->afterInitialize($this->subjectMock, $this->productMock)
        );
    }

    public function testAfterInitializeWithUserWithoutRole()
    {
        $this->userMock->expects($this->once())->method('getRole')->will($this->returnValue(null));
        $this->authSessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue(true));
        $this->authSessionMock->expects($this->once())->method('getUser')->will($this->returnValue($this->userMock));
        $this->pricePermDataMock->expects($this->never())->method('getCanAdminEditProductPrice');
        $this->productHandlerMock->expects($this->once())->method('handle')->with($this->productMock);

        $this->assertEquals(
            $this->productMock,
            $this->_model->afterInitialize($this->subjectMock, $this->productMock)
        );
    }

    public function testAfterInitializeWhenAdminCanNotEditProductPrice()
    {
        $this->userMock->expects($this->once())->method('getRole')->will($this->returnValue(1));
        $this->authSessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue(true));
        $this->authSessionMock->expects($this->once())->method('getUser')->will($this->returnValue($this->userMock));
        $this->pricePermDataMock->expects(
            $this->once()
        )->method(
            'getCanAdminEditProductPrice'
        )->will(
            $this->returnValue(false)
        );

        $this->productHandlerMock->expects($this->once())->method('handle')->with($this->productMock);

        $this->assertEquals(
            $this->productMock,
            $this->_model->afterInitialize($this->subjectMock, $this->productMock)
        );
    }

    public function testAfterInitializeWhenAdminCanEditProductPrice()
    {
        $this->userMock->expects($this->once())->method('getRole')->will($this->returnValue(1));
        $this->authSessionMock->expects($this->once())->method('isLoggedIn')->will($this->returnValue(true));
        $this->authSessionMock->expects($this->once())->method('getUser')->will($this->returnValue($this->userMock));
        $this->pricePermDataMock->expects(
            $this->once()
        )->method(
            'getCanAdminEditProductPrice'
        )->will(
            $this->returnValue(true)
        );
        $this->productHandlerMock->expects($this->never())->method('handle');

        $this->assertEquals(
            $this->productMock,
            $this->_model->afterInitialize($this->subjectMock, $this->productMock)
        );
    }
}
