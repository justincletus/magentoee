<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\AdvancedCheckout\Test\Unit\Model\Observer;

class UploadSkuCsvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UploadSkuCsv
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cartProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $checkoutDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cartMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    protected function setUp()
    {
        $this->checkoutDataMock = $this->getMock('Magento\AdvancedCheckout\Helper\Data', [], [], '', false);
        $this->cartProviderMock =
            $this->getMock('Magento\AdvancedCheckout\Model\Observer\CartProvider', [], [], '', false);
        $this->cartMock = $this->getMock(
            'Magento\AdvancedCheckout\Model\Cart',
            [
                'prepareAddProductsBySku',
                'saveAffectedProducts',
                '__wakeup'
            ],
            [],
            '',
            false);
        $this->observerMock = $this->getMock(
            'Magento\Framework\Event\Observer',
            [
                'getRequestModel',
                'getOrderCreateModel',
                '__wakeup'
            ],
            [],
            '',
            false);

        $this->model = new \Magento\AdvancedCheckout\Model\Observer\UploadSkuCsv($this->checkoutDataMock, $this->cartProviderMock);
    }

    public function testExecuteWhenSkuFileIsNotUploaded()
    {
        $requestInterfaceMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->observerMock->expects($this->once())
            ->method('getRequestModel')->will($this->returnValue($requestInterfaceMock));
        $this->checkoutDataMock->expects($this->once())
            ->method('isSkuFileUploaded')->with($requestInterfaceMock)->will($this->returnValue(false));
        $this->checkoutDataMock->expects($this->never())->method('processSkuFileUploading');

        $this->model->execute($this->observerMock);
    }

    public function testExecute()
    {
        $requestInterfaceMock = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->observerMock->expects($this->once())
            ->method('getRequestModel')->will($this->returnValue($requestInterfaceMock));
        $this->checkoutDataMock->expects($this->once())
            ->method('isSkuFileUploaded')->with($requestInterfaceMock)->will($this->returnValue(true));
        $this->checkoutDataMock->expects($this->once())
            ->method('processSkuFileUploading')->will($this->returnValue(['one']));
        $orderCreateModelMock = $this->getMock('Magento\Sales\Model\AdminOrder\Create', [], [], '', false);
        $this->observerMock->expects($this->once())
            ->method('getOrderCreateModel')->will($this->returnValue($orderCreateModelMock));
        $this->cartProviderMock->expects($this->once())
            ->method('get')->with($this->observerMock)->will($this->returnValue($this->cartMock));
        $this->cartMock->expects($this->once())->method('prepareAddProductsBySku')->with(['one']);
        $this->cartMock->expects($this->once())->method('saveAffectedProducts')->with($orderCreateModelMock, false);

        $this->model->execute($this->observerMock);
    }
}
