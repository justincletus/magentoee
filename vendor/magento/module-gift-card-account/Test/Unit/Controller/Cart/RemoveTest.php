<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardAccount\Test\Unit\Controller\Cart;

class RemoveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCardAccount\Controller\Cart\Remove
     */
    protected $removeController;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $redirectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $giftCardAccountMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $escaperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectMock;

    protected function setUp()
    {
        $this->contextMock = $this->getMock('Magento\Framework\App\Action\Context', [], [], '', false);
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->messageManagerMock = $this->getMock('Magento\Framework\Message\ManagerInterface');
        $this->resultRedirectFactory =
            $this->getMock('Magento\Framework\Controller\Result\RedirectFactory', [], [], '', false);
        $this->redirectMock = $this->getMock('Magento\Framework\App\Response\RedirectInterface');
        $this->scopeConfigMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->requestMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->resultRedirectMock = $this->getMock('\Magento\Framework\Controller\Result\Redirect', [], [], '', false);
        $this->contextMock->expects($this->once())->method('getObjectManager')->willReturn($this->objectManagerMock);
        $this->contextMock->expects($this->once())->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->contextMock->expects($this->once())->method('getRedirect')->willReturn($this->redirectMock);
        $this->contextMock
            ->expects($this->once())
            ->method('getResultRedirectFactory')
            ->willReturn($this->resultRedirectFactory);
        $this->contextMock->expects($this->once())->method('getRequest')->willReturn($this->requestMock);
        $this->giftCardAccountMock = $this->getMock('Magento\GiftCardAccount\Model\Giftcardaccount', [], [], '', false);
        $this->escaperMock = $this->getMock('Magento\Framework\Escaper', [], [], '', false);
        $this->removeController = new \Magento\GiftCardAccount\Controller\Cart\Remove(
            $this->contextMock,
            $this->scopeConfigMock,
            $this->getMock('Magento\Checkout\Model\Session', [], [], '', false),
            $this->getMock('Magento\Store\Model\StoreManagerInterface', [], [], '', false),
            $this->getMock('Magento\Framework\Data\Form\FormKey\Validator', [], [], '', false),
            $this->getMock('Magento\Checkout\Model\Cart', [], [], '', false)
        );
    }

    public function testExecute()
    {
        $valueMap = [
            ['code', null, 'gift_card_code'],
            ['return_url', null, false],
            ['in_cart', null, false]
        ];
        $this->requestMock->expects($this->any())->method('getParam')->willReturnMap($valueMap);
        $this->objectManagerMock
            ->expects($this->once())
            ->method('create')
            ->with('Magento\GiftCardAccount\Model\Giftcardaccount')
            ->willReturn($this->giftCardAccountMock);
        $this->giftCardAccountMock
            ->expects($this->once())
            ->method('loadByCode')
            ->with('gift_card_code')
            ->willReturn($this->giftCardAccountMock);
        $this->giftCardAccountMock->expects($this->once())->method('removeFromCart');
        $this->objectManagerMock
            ->expects($this->once())
            ->method('get')
            ->with('Magento\Framework\Escaper')
            ->willReturn($this->escaperMock);
        $this->escaperMock->expects($this->once())->method('escapeHtml')->with('gift_card_code');
        $this->resultRedirectFactory->expects($this->once())->method('create')->willReturn($this->resultRedirectMock);
        $this->redirectMock->expects($this->once())->method('getRefererUrl')->willReturn('http://example.com');
        $this->scopeConfigMock->expects($this->once())->method('getValue')->willReturn(false);
        $this->resultRedirectMock
            ->expects($this->once())
            ->method('setUrl')
            ->with('http://example.com')
            ->willReturnSelf();
        $this->messageManagerMock->expects($this->once())->method('addSuccess');
        $this->assertEquals($this->resultRedirectMock, $this->removeController->execute());
    }
}
