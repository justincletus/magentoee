<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Worldpay\Test\Unit\Gateway\Request\RemoteAdmin;

use Magento\Worldpay\Gateway\Request\RemoteAdmin\TransactionDataBuilder;

class TransactionDataBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var TransactionDataBuilder
     */
    private $builder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $orderAdapter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $paymentDO;

    public function setUp()
    {
        $this->config = $this->getMockBuilder(
            'Magento\Payment\Gateway\ConfigInterface'
        )
            ->getMockForAbstractClass();
        $this->paymentDO = $this->getMockBuilder(
            'Magento\Payment\Gateway\Data\PaymentDataObjectInterface'
        )
            ->getMockForAbstractClass();
        $this->orderAdapter = $this->getMockBuilder(
            'Magento\Payment\Gateway\Data\OrderAdapterInterface'
        )
            ->getMockForAbstractClass();

        $this->builder = new TransactionDataBuilder($this->config);
    }

    public function testBuild()
    {
        $storeId = 1;

        $expectation = [
            'authPW' => 'PASSWORD',
            'instId' => 'ADMIN_ID',
            'testMode' => 100
        ];

        $this->paymentDO->expects(static::any())
            ->method('getOrder')
            ->willReturn($this->orderAdapter);
        $this->orderAdapter->expects(static::any())
            ->method('getStoreId')
            ->willReturn($storeId);
        $this->config->expects(static::any())
            ->method('getValue')
            ->willReturnMap(
                [
                    ['auth_password', $storeId, 'PASSWORD'],
                    ['admin_installation_id', $storeId, 'ADMIN_ID'],
                    ['test_mode', $storeId, '1']
                ]
            );

        static::assertEquals(
            $expectation,
            $this->builder->build(['payment' => $this->paymentDO])
        );
    }
}
