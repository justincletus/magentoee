<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Test\Unit\Controller\Adminhtml\Products;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class MassAssignTest extends \PHPUnit_Framework_TestCase
{
    /*
     * @var \Magento\VisualMerchandiser\Controller\Adminhtml\Products\MassAssign
     */
    protected $controller;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * Magento\Framework\DataObject
     */
    protected $response;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Api\Data\ProductInterface
     */
    protected $product;

    /**
     * @var \Magento\Framework\Controller\Result\Json|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultJson;

    /**
     * @var \Magento\VisualMerchandiser\Model\Position\Cache|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cache;

    /**
     * Set up instances and mock objects
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp()
    {
        $this->context = $this->getMock('Magento\Backend\App\Action\Context', [], [], '', false);

        $this->request = $this->getMock('Magento\Framework\App\RequestInterface', [], [], '', false);

        $this->objectManager = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageManager = $this->getMockBuilder('\Magento\Framework\Message\ManagerInterface')
            ->disableOriginalConstructor()
            ->setMethods(['hasMessages'])
            ->getMockForAbstractClass();

        $this->layout = $this->getMockBuilder('Magento\Framework\View\LayoutInterface')
            ->disableOriginalConstructor()
            ->setMethods(['initMessages'])
            ->getMockForAbstractClass();

        $this->resultJson = $this->getMockBuilder('Magento\Framework\Controller\Result\Json')
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultJson
            ->expects($this->any())
            ->method('setJsonData')
            ->willReturn($this->resultJson);

        $this->product = $this->getMockBuilder('\Magento\Catalog\Api\Data\ProductInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->product
            ->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $this->productRepository = $this->getMockBuilder('\Magento\Catalog\Api\ProductRepositoryInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->productRepository
            ->expects($this->any())
            ->method('get')
            ->willReturn($this->product);

        $this->cache = $this->getMockBuilder('\Magento\VisualMerchandiser\Model\Position\Cache')
            ->disableOriginalConstructor()
            ->setMethods(['prependPositions', 'getPositions', 'saveData'])
            ->getMock();

        $resultJsonFactory = $this->getMockBuilder('Magento\Framework\Controller\Result\JsonFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $resultJsonFactory
            ->expects($this->any())
            ->method('create')
            ->willReturn($this->resultJson);

        $this->context
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->request));

        $this->context
            ->expects($this->any())
            ->method('getObjectManager')
            ->willReturn($this->objectManager);

        $this->context
            ->expects($this->any())
            ->method('getMessageManager')
            ->willReturn($this->messageManager);

        $this->response = $this->getMock('Magento\Framework\DataObject', ['setError'], [], '', false);

        $this->objectManager
            ->expects($this->any())
            ->method('create')
            ->willReturn($this->response);

        $this->layoutFactory = $this->getMockBuilder('Magento\Framework\View\LayoutFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->layoutFactory
            ->expects($this->any())
            ->method('create')
            ->willReturn($this->layout);

        $messagesBlock = $this->getMockBuilder('\Magento\Framework\View\Element\Messages')
            ->disableOriginalConstructor()
            ->getMock();

        $this->layout
            ->expects($this->any())
            ->method('getMessagesBlock')
            ->willReturn($messagesBlock);

        $this->controller = (new ObjectManager($this))->getObject(
            'Magento\VisualMerchandiser\Controller\Adminhtml\Products\MassAssign',
            [
                'context' => $this->context,
                'layoutFactory' => $this->layoutFactory,
                'resultJsonFactory' => $resultJsonFactory,
                'productRepository' => $this->productRepository,
                'cache' => $this->cache
            ]
        );
    }

    /**
     * Test execute assign method
     */
    public function testExecuteAssign()
    {
        $map = [
            ['action', null, 'assign'],
            [\Magento\VisualMerchandiser\Model\Position\Cache::POSITION_CACHE_KEY, null, 'xxx'],
            ['add_product_sku', null, '24-MB01']
        ];

        $this->request
            ->expects($this->atLeastOnce())
            ->method('getParam')
            ->will($this->returnValueMap($map));

        $this->assertInstanceOf(
            '\Magento\Framework\Controller\Result\Json',
            $this->controller->execute()
        );
    }

    /**
     * Test execute remove method
     */
    public function testExecuteRemove()
    {
        $map = [
            ['action', null, 'remove'],
            [\Magento\VisualMerchandiser\Model\Position\Cache::POSITION_CACHE_KEY, null, 'xxx'],
            ['add_product_sku', null, '24-MB01']
        ];

        $this->cache->expects($this->any())
            ->method('getPositions')
            ->willReturn([1 => 0]);
        $this->cache->expects($this->once())
            ->method('saveData')
            ->with('xxx', [], null);

        $this->request
            ->expects($this->atLeastOnce())
            ->method('getParam')
            ->will($this->returnValueMap($map));

        $this->assertInstanceOf(
            '\Magento\Framework\Controller\Result\Json',
            $this->controller->execute()
        );
    }
}
