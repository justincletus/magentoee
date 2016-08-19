<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Test\Unit\Controller;

/**
 * Class ReturnsTest
 * @package Magento\Rma\Controller
 */
abstract class ReturnsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var Returns
     */
    protected $controller;

    /**
     * @var \Magento\Framework\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\App\Request\Http | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Response\Http | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * @var \Magento\Framework\ObjectManagerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Url | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $url;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface  | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $redirect;

    /**
     * @var \Magento\Framework\Message\Manager | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManager;

    protected function initContext()
    {
        $this->context = $this->getMock('Magento\Framework\App\Action\Context', [], [], '', false);
        $this->context->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $this->context->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($this->response));
        $this->context->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $this->context->expects($this->once())
            ->method('getMessageManager')
            ->will($this->returnValue($this->messageManager));
        $this->context->expects($this->once())
            ->method('getRedirect')
            ->will($this->returnValue($this->redirect));
        $this->context->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($this->url));
    }

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->response = $this->getMock('Magento\Framework\App\Response\Http', [], [], '', false);
        $this->objectManager = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->messageManager = $this->getMock('Magento\Framework\Message\Manager', [], [], '', false);
        $this->redirect = $this->getMock('Magento\Store\App\Response\Redirect', [], [], '', false);
        $this->url = $this->getMock('Magento\Framework\Url', [], [], '', false);

        $this->initContext();

        $this->coreRegistry = $this->getMock('Magento\Framework\Registry', [], [], '', false);

        $this->controller = $objectManagerHelper->getObject(
            '\\Magento\\Rma\\Controller\\Returns\\' . $this->name,
            [
                'coreRegistry' => $this->coreRegistry,
                'context' => $this->context
            ]
        );
    }
}
