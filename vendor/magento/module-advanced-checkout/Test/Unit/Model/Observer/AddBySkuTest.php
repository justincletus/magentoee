<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\AdvancedCheckout\Test\Unit\Model\Observer;

class AddBySkuTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AddBySku
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cartMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cartProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCreateModelMock;

    protected function setUp()
    {
        $this->cartMock = $this->getMock('Magento\AdvancedCheckout\Model\Cart', [], [], '', false);
        $this->cartProviderMock =
            $this->getMock(
                'Magento\AdvancedCheckout\Model\Observer\CartProvider',
                [
                    'removeAllAffectedItems',
                    'removeAffectedItem',
                    'prepareAddProductBySku',
                    'saveAffectedProducts',
                    'get',
                    '__wakeup'
                ],
                [],
                '',
                false);
        $this->observerMock = $this->getMock(
            '\Magento\Framework\Event\Observer',
            [
                'getRequestModel',
                'getOrderCreateModel',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->requestMock = $this->getMock(
            'Magento\Framework\App\Request',
            [
                'getPost',
                'setPostValue',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->orderCreateModelMock = $this->getMock('Magento\Sales\Model\AdminOrder\Create', [], [], '', false);

        $this->model = new \Magento\AdvancedCheckout\Model\Observer\AddBySku($this->cartMock, $this->cartProviderMock);
    }

    public function testExecuteWithEmptyRequestAndCart()
    {
        $this->observerMock->expects($this->once())
            ->method('getRequestModel')
            ->will($this->returnValue(null));
        $this->cartProviderMock->expects($this->once())
            ->method('get')
            ->with($this->observerMock)
            ->will($this->returnValue(null));
        $this->requestMock->expects($this->never())->method('getPost');

        $this->model->execute($this->observerMock);
    }

    public function testExecuteWithRemoveFailedAndFromErrorGrid()
    {
        $this->observerMock->expects($this->once())
            ->method('getRequestModel')
            ->will($this->returnValue($this->requestMock));
        $this->cartProviderMock->expects($this->once())
            ->method('get')
            ->with($this->observerMock)
            ->will($this->returnValue($this->cartProviderMock));
        $postParams =
            [
                ['sku_remove_failed', true],
                ['from_error_grid', true],
            ];
        $this->requestMock->expects($this->any())
            ->method('getPost')
            ->will($this->returnValueMap($postParams));
        $this->cartProviderMock->expects($this->once())->method('removeAllAffectedItems');

        $this->model->execute($this->observerMock);
    }

    public function testExecuteWithSku()
    {
        $this->observerMock->expects($this->exactly(2))
            ->method('getRequestModel')
            ->will($this->returnValue($this->requestMock));
        $this->cartProviderMock->expects($this->exactly(2))
            ->method('get')
            ->with($this->observerMock)
            ->will($this->returnValue($this->cartProviderMock));
        $postParams =
            [
                ['sku_remove_failed', false],
                ['remove_sku', false, 'some_sku_123' ],
            ];
        $this->requestMock->expects($this->any())
            ->method('getPost')
            ->will($this->returnValueMap($postParams));
        $this->cartProviderMock->expects($this->once())->method('removeAffectedItem')->with('some_sku_123');

        $this->model->execute($this->observerMock);
    }

    public function testExecuteWithoutAddBySkuItems()
    {
        $this->observerMock->expects($this->exactly(2))
            ->method('getRequestModel')
            ->will($this->returnValue($this->requestMock));
        $this->cartProviderMock->expects($this->exactly(1))
            ->method('get')
            ->with($this->observerMock)
            ->will($this->returnValue($this->cartProviderMock));
        $postParams =
            [
                ['sku_remove_failed', false],
                ['remove_sku', false, null],
                [\Magento\AdvancedCheckout\Block\Adminhtml\Sku\AbstractSku::LIST_TYPE, [], null],
                ['item', [], []],
            ];
        $this->requestMock->expects($this->any())
            ->method('getPost')
            ->will($this->returnValueMap($postParams));

        $this->model->execute($this->observerMock);
    }

    public function testExecute()
    {
        $this->observerMock->expects($this->exactly(2))
            ->method('getRequestModel')
            ->will($this->returnValue($this->requestMock));
        $this->cartProviderMock->expects($this->exactly(1))
            ->method('get')
            ->with($this->observerMock)
            ->will($this->returnValue($this->cartProviderMock));
        $addBySkuItems =
            [
                0 => [
                    'sku' => 'some_sku',
                    'qty' => 11,
                ],
            ];
        $postParams =
            [
                ['sku_remove_failed', false],
                ['remove_sku', false, null],
                [\Magento\AdvancedCheckout\Block\Adminhtml\Sku\AbstractSku::LIST_TYPE, [], $addBySkuItems],
                ['item', [], []],
            ];
        $this->requestMock->expects($this->any())
            ->method('getPost')
            ->will($this->returnValueMap($postParams));
        $this->cartProviderMock->expects($this->once())->method('prepareAddProductBySku')->with('some_sku', 11, []);
        $this->observerMock->expects($this->once())
            ->method('getOrderCreateModel')
            ->will($this->returnValue($this->orderCreateModelMock));
        $this->cartProviderMock->expects($this->once())
            ->method('saveAffectedProducts')
            ->with($this->orderCreateModelMock, false);
        $this->requestMock->expects($this->once())->method('setPostValue')->with('item', []);

        $this->model->execute($this->observerMock);
    }
}
