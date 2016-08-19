<?php
/***
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Test\Unit\Controller\Adminhtml\Position;

class SaveTest extends \PHPUnit_Framework_TestCase
{
    /*
     * @var \Magento\VisualMerchandiser\Controller\Adminhtml\Position\Save
     */
    protected $controller;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\Controller\Result\Json|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultJson;

    /**
     * Set up instances and mock objects
     */
    protected function setUp()
    {
        $this->requestMock = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);

        $this->resultJson = $this->getMockBuilder('Magento\Framework\Controller\Result\Json')
            ->disableOriginalConstructor()
            ->getMock();

        $resultJsonFactory = $this->getMockBuilder('Magento\Framework\Controller\Result\JsonFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $resultJsonFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->resultJson);

        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $context = $this->getMock(
            'Magento\Backend\App\Action\Context',
            ['getRequest'],
            $helper->getConstructArguments(
                'Magento\Backend\App\Action\Context'
            )
        );
        $cache = $this->getMock(
            'Magento\VisualMerchandiser\Model\Position\Cache',
            [],
            $helper->getConstructArguments(
                'Magento\VisualMerchandiser\Model\Position\Cache'
            )
        );
        $context->expects($this->once())->method('getRequest')->will($this->returnValue($this->requestMock));
        $this->controller = new \Magento\VisualMerchandiser\Controller\Adminhtml\Position\Save(
            $context,
            $cache,
            $resultJsonFactory
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $this->assertInstanceOf(
            '\Magento\Framework\Controller\Result\Json',
            $this->controller->execute()
        );
    }
}
