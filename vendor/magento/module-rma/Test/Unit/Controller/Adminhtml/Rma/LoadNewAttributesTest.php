<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Test\Unit\Controller\Adminhtml\Rma;

class LoadNewAttributesTest extends \Magento\Rma\Test\Unit\Controller\Adminhtml\RmaTest
{
    protected $name = 'LoadNewAttributes';

    /**
     * @var \Magento\Framework\Json\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    public function setUp()
    {
        parent::setUp();
        $this->helperMock = $this->getMockBuilder('Magento\Framework\Json\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testLoadNewAttributesActionWithoutUserAttributes()
    {
        $itemId = 2;
        $productId = 1;
        $rmaMock = $this->getMock('Magento\Rma\Model\Item', [], [], '', false);
        $layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);
        $blockMock = $this->getMock(
            'Magento\Framework\View\Element\Template',
            ['setProductId', 'initForm'],
            [],
            '',
            false
        );

        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('product_id', null)
            ->will($this->returnValue($productId));
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('item_id')
            ->will($this->returnValue($itemId));
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Rma\Model\Item', [])
            ->will($this->returnValue($rmaMock));
        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));

        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('magento_rma_edit_item')
            ->will($this->returnValue($blockMock));
        $blockMock->expects($this->once())
            ->method('setProductId')
            ->with($productId)
            ->will($this->returnSelf());
        $blockMock->expects($this->once())
            ->method('initForm')
            ->will($this->returnSelf());

        $this->responseMock->expects($this->never())
            ->method('setBody');

        $this->assertNull($this->action->execute());
    }

    public function testLoadNewAttributeActionResponseArray()
    {
        $itemId = 2;
        $productId = 1;
        $responseArray = ['html', 'html'];
        $responseString = 'json';
        $rmaMock = $this->getMock('Magento\Rma\Model\Item', [], [], '', false);
        $layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);
        $blockMock = $this->getMock(
            'Magento\Framework\View\Element\Template',
            ['setProductId', 'initForm'],
            [],
            '',
            false
        );

        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('product_id', null)
            ->will($this->returnValue($productId));
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('item_id')
            ->will($this->returnValue($itemId));
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Rma\Model\Item', [])
            ->will($this->returnValue($rmaMock));
        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));

        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('magento_rma_edit_item')
            ->will($this->returnValue($blockMock));
        $blockMock->expects($this->once())
            ->method('setProductId')
            ->with($productId)
            ->will($this->returnSelf());

        $blockMock->expects($this->once())
            ->method('initForm')
            ->will($this->returnValue($this->formMock));

        $this->formMock->expects($this->once())
            ->method('hasNewAttributes')
            ->will($this->returnValue(true));
        $this->formMock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($responseArray));
        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Framework\Json\Helper\Data')
            ->will($this->returnValue($this->helperMock));
        $this->helperMock->expects($this->once())
            ->method('jsonEncode')
            ->with($responseArray)
            ->will($this->returnValue($responseString));
        $this->responseMock->expects($this->once())
            ->method('representJson')
            ->with($responseString);
        $this->responseMock->expects($this->never())
            ->method('setBody');
        $this->assertNull($this->action->execute());
    }

    public function testLoadNewAttributesActionResponseString()
    {
        $itemId = 2;
        $productId = 1;
        $responseString = 'json';
        $rmaMock = $this->getMock('Magento\Rma\Model\Item', [], [], '', false);
        $layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);
        $blockMock = $this->getMock(
            'Magento\Framework\View\Element\Template',
            ['setProductId', 'initForm'],
            [],
            '',
            false
        );

        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('product_id', null)
            ->will($this->returnValue($productId));
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('item_id')
            ->will($this->returnValue($itemId));
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Rma\Model\Item', [])
            ->will($this->returnValue($rmaMock));
        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));

        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('magento_rma_edit_item')
            ->will($this->returnValue($blockMock));
        $blockMock->expects($this->once())
            ->method('setProductId')
            ->with($productId)
            ->will($this->returnSelf());

        $blockMock->expects($this->once())
            ->method('initForm')
            ->will($this->returnValue($this->formMock));

        $this->formMock->expects($this->once())
            ->method('hasNewAttributes')
            ->will($this->returnValue(true));
        $this->formMock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($responseString));
        $this->helperMock->expects($this->never())
            ->method('jsonEncode');
        $this->responseMock->expects($this->never())
            ->method('representJson');
        $this->responseMock->expects($this->once())
            ->method('setBody')
            ->with($responseString);
        $this->assertNull($this->action->execute());
    }

    public function testLoadNewAttributesAction()
    {
        $blockHtml = 'test';
        $productId = 1;
        $itemId = 2;
        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('product_id')
            ->will($this->returnValue($productId));
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('item_id')
            ->will($this->returnValue($itemId));

        $rmaBlockMock = $this->getMockBuilder('Magento\Rma\Block\Adminhtml\Rma\Edit\Item')
            ->disableOriginalConstructor()
            ->setMethods(['setProductId', 'setHtmlPrefixId', 'initForm', 'hasNewAttributes', 'toHtml'])
            ->getMock();

        $rmaBlockMock->expects($this->once())
            ->method('setProductId')
            ->with($productId)
            ->will($this->returnSelf());
        $rmaBlockMock->expects($this->once())
            ->method('setHtmlPrefixId')
            ->with($itemId)
            ->will($this->returnSelf());
        $rmaBlockMock->expects($this->once())
            ->method('initForm')
            ->will($this->returnSelf());
        $rmaBlockMock->expects($this->once())
            ->method('hasNewAttributes')
            ->will($this->returnValue(true));
        $rmaBlockMock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($blockHtml));

        $layoutMock = $this->getMockBuilder('Magento\Framework\View\Layout')
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('magento_rma_edit_item')
            ->will($this->returnValue($rmaBlockMock));

        $this->viewMock->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));

        $this->assertNull($this->action->execute());
    }
}
