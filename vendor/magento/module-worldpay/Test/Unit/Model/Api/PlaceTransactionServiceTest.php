<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Worldpay\Test\Unit\Model\Api;

use Magento\Worldpay\Model\Api\PlaceTransactionService;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Worldpay\Gateway\Command\Form\BuildCommand;

/**
 * Class PlaceTransactionServiceTest
 */
class PlaceTransactionServiceTest extends \PHPUnit_Framework_TestCase
{
    const ORDER_ID = 1111;
    /**
     * @var OrderRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $orderRepositoryMock;

    /**
     * @var BuildCommand|\PHPUnit_Framework_MockObject_MockObject
     */
    private $buildCommandMock;

    /**
     * @var PaymentDataObjectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $paymentDataObjectFactoryMock;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp()
    {
        $this->orderRepositoryMock = $this->getMockBuilder(
            'Magento\Sales\Api\OrderRepositoryInterface'
        )->getMockForAbstractClass();

        $this->buildCommandMock = $this->getMockBuilder(
            'Magento\Worldpay\Gateway\Command\Form\BuildCommand'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->paymentDataObjectFactoryMock = $this->getMockBuilder(
            'Magento\Payment\Gateway\Data\PaymentDataObjectFactory'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->placeTransactionService = new PlaceTransactionService(
            $this->orderRepositoryMock,
            $this->buildCommandMock,
            $this->paymentDataObjectFactoryMock
        );
    }

    /**
     * @covers \Magento\Worldpay\Model\Api\PlaceTransactionService::placeTransaction
     * @return void
     */
    public function testPlaceTransaction()
    {
        $result = [
            'action' => 'action value',
            'fields' => [
                'MC_order_id' => self::ORDER_ID,
                'MC_store_id' => '1',
                'authAmountString' => 'US&#36;15.00'
            ]
        ];

        $orderMock = $this->getMockBuilder(
            'Magento\Sales\Api\Data\OrderInterface'
        )->getMockForAbstractClass();
        $paymentMock = $this->getMockBuilder(
            'Magento\Payment\Model\InfoInterface'
        )->getMockForAbstractClass();
        $paymentDO = $this->getMockBuilder(
            'Magento\Payment\Gateway\Data\PaymentDataObjectInterface'
        )
            ->getMockForAbstractClass();
        $arrayResultMock = $this->getMockBuilder(
            'Magento\Payment\Gateway\Command\Result\ArrayResult'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $orderMock->expects(static::once())
            ->method('getPayment')
            ->willReturn($paymentMock);
        $this->orderRepositoryMock->expects(static::once())
            ->method('get')
            ->with(self::ORDER_ID)
            ->willReturn($orderMock);
        $this->paymentDataObjectFactoryMock->expects(static::once())
            ->method('create')
            ->with($paymentMock)
            ->willReturn($paymentDO);
        $this->buildCommandMock->expects(static::once())
            ->method('execute')
            ->with(['payment' => $paymentDO])
            ->willReturn($arrayResultMock);
        $arrayResultMock->expects(static::once())
            ->method('get')
            ->willReturn($result);

        static::assertEquals([
            'action' => $result['action'],
            'fields' => array_keys($result['fields']),
            'values' => array_values($result['fields'])
        ], $this->placeTransactionService->placeTransaction(self::ORDER_ID));
    }
}
