<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerBalance\Test\Unit\Observer;

/**
 * Class CreditmemoDataImportObserverTest
 */
class CreditmemoDataImportObserverTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CustomerBalance\Observer\CreditmemoDataImportObserver */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceCurrencyMock;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    protected $observer;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $event;

    protected function setUp()
    {
        $this->priceCurrencyMock = $this->getMockBuilder('Magento\Directory\Model\PriceCurrency')
            ->disableOriginalConstructor()
            ->getMock();

        $this->event = new \Magento\Framework\DataObject();
        $this->observer = new \Magento\Framework\Event\Observer(['event' => $this->event]);

        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject(
            'Magento\CustomerBalance\Observer\CreditmemoDataImportObserver',
            [
                'priceCurrency' => $this->priceCurrencyMock,
            ]
        );
    }

    public function testCreditmemoDataImport()
    {
        $refundAmount = 10;
        $rate = 2;
        $dataInput = [
            'refund_customerbalance_return' => $refundAmount,
            'refund_customerbalance_return_enable' => true,
            'refund_customerbalance' => true,
            'refund_real_customerbalance' => true,
        ];

        $observerMock = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();
        $creditmemoMock = $this->getMockBuilder('Magento\Sales\Model\Order\Creditmemo')
            ->disableOriginalConstructor()
            ->setMethods(['getBaseCustomerBalanceReturnMax', 'getOrder'])
            ->getMock();
        $creditmemoMock->expects($this->once())
            ->method('getBaseCustomerBalanceReturnMax')
            ->willReturn($refundAmount);

        $this->priceCurrencyMock->expects($this->at(0))
            ->method('round')
            ->with($refundAmount)
            ->willReturnArgument(0);
        $this->priceCurrencyMock->expects($this->at(1))
            ->method('round')
            ->with($refundAmount * $rate)
            ->willReturnArgument(0);

        $orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->setMethods(['getBaseToOrderRate'])
            ->getMock();
        $orderMock->expects($this->once())
            ->method('getBaseToOrderRate')
            ->willReturn($rate);

        $creditmemoMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($orderMock);

        $eventMock = $this->getMockBuilder('Magento\Framework\Event')
            ->disableOriginalConstructor()
            ->setMethods(['getCreditmemo', 'getInput'])
            ->getMock();
        $eventMock->expects($this->once())
            ->method('getCreditmemo')
            ->willReturn($creditmemoMock);
        $eventMock->expects($this->once())
            ->method('getInput')
            ->willReturn($dataInput);
        $observerMock->expects($this->any())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->assertEquals($this->model, $this->model->execute($observerMock));
    }
}
