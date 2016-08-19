<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCheckout\Test\Unit\Controller\Adminhtml;

/**
 * Tests for AdvancedCheckout Index
 */
class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdvancedCheckout\Test\Unit\Controller\Adminhtml\Stub\Child
     */
    protected $controller;

    /**
     * @var \Magento\Framework\ObjectManagerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\Request\Http | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerFactory;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp()
    {
        $this->objectManager = $this->getMock('Magento\Framework\ObjectManager\ObjectManager', [], [], '', false);
        $this->customerFactory = $this->getMock(
            'Magento\Customer\Api\Data\CustomerInterfaceFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->request = $this->getMock('Magento\Framework\App\Request\Http', ['getPost', 'getParam'], [], '', false);
        $response = $this->getMock('Magento\Framework\App\ResponseInterface');

        $context = $this->getMock('Magento\Backend\App\Action\Context', [], [], '', false);
        $context->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->request);
        $context->expects($this->once())
            ->method('getResponse')
            ->willReturn($response);
        $context->expects($this->once())
            ->method('getObjectManager')
            ->willReturn($this->objectManager);
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->controller = $objectManager->getObject(
            'Magento\AdvancedCheckout\Test\Unit\Controller\Adminhtml\Stub\Child',
            ['context' => $context, 'customerFactory' => $this->customerFactory]
        );
    }

    /**
     * Test AdvancedCheckoutIndex InitData with Quote id false
     *
     * @return void
     */
    public function testInitData()
    {
        $this->request->expects($this->any())
            ->method('getParam')
            ->willReturn(true);

        $customerModel = $this->getMock(
            'Magento\Customer\Model\Customer',
            ['getWebsiteId', 'load', 'getId', 'getData'],
            [],
            '',
            false
        );
        $customerModel->expects($this->once())
            ->method('load')
            ->willReturnSelf();
        $customerId = 1;
        $customerModel->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($customerId);
        $customerModel->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(true);

        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);

        $storeManager = $this->getMock('Magento\Store\Model\StoreManager', ['getWebsiteId', 'getStore'], [], '', false);
        $storeManager->expects($this->any())
            ->method('getStore')
            ->willReturn($store);

        $quote = $this->getMock('Magento\Quote\Model\Quote', [], [], '', false);
        $quote->expects($this->once())
            ->method('getId')
            ->willReturn(false);

        $cart = $this->getMock('Magento\AdvancedCheckout\Model\Cart', [], [], '', false);
        $cart->expects($this->once())
            ->method('setSession')
            ->willReturnSelf();
        $cart->expects($this->once())
            ->method('setContext')
            ->willReturnSelf();
        $cart->expects($this->once())
            ->method('setCurrentStore')
            ->willReturnSelf();
        $cart->expects($this->once())
            ->method('getQuote')
            ->willReturn($quote);

        $session = $this->getMock('Magento\Backend\Model\Session', [], [], '', false);
        $quoteRepository = $this->getMock('\Magento\Quote\Api\CartRepositoryInterface');

        $this->objectManager->expects($this->at(0))
            ->method('create')
            ->with('Magento\Customer\Model\Customer')
            ->willReturn($customerModel);
        $this->objectManager->expects($this->at(1))
            ->method('get')
            ->with('Magento\Store\Model\StoreManager')
            ->willReturn($storeManager);
        $this->objectManager->expects($this->at(2))
            ->method('get')
            ->with('Magento\AdvancedCheckout\Model\Cart')
            ->willReturn($cart);
        $this->objectManager->expects($this->at(3))
            ->method('get')
            ->with('Magento\Backend\Model\Session')
            ->willReturn($session);
        $this->objectManager->expects($this->at(4))
            ->method('get')
            ->with('Magento\Quote\Api\CartRepositoryInterface')
            ->willReturn($quoteRepository);
        $customerData = $this->expectCustomerModelConvertToCustomerData($customerModel, $customerId);
        $quote->expects($this->once())
            ->method('setCustomer')
            ->with($customerData);
        $quote->expects($this->once())
            ->method('setStore')
            ->willReturnSelf();

        $this->controller->execute();
    }

    /**
     * Expecting for converting Customer Model
     *
     * @param $customerModel
     * @param $customerId
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function expectCustomerModelConvertToCustomerData($customerModel, $customerId)
    {
        $customerData = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\CustomerInterface',
            [],
            '',
            false
        );
        $customerData->expects($this->once())
            ->method('setId')
            ->with($customerId)
            ->willReturnSelf();

        $this->customerFactory->expects($this->once())
            ->method('create')
            ->willReturn($customerData);

        $customerDataArray = ['entity_id' => 1];
        $customerModel->expects($this->once())
            ->method('getData')
            ->willReturn($customerDataArray);

        return $customerData;
    }
}
