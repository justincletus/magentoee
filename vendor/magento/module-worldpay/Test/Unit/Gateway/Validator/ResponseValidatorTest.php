<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Worldpay\Test\Unit\Gateway\Validator;

use Magento\Worldpay\Gateway\Request\HtmlRedirect\OrderDataBuilder;
use Magento\Worldpay\Gateway\Validator\ResponseValidator;

class ResponseValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resultFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $remoteAddress;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $orderRepository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resultMock;

    /**
     * @var ResponseValidator
     */
    private $validator;

    protected function setUp()
    {
        $this->resultFactory = $this->getMockBuilder(
            'Magento\Payment\Gateway\Validator\ResultInterfaceFactory'
        )
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->request = $this->getMockBuilder(
            'Magento\Framework\App\Request\Http'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->remoteAddress = $this->getMockBuilder(
            'Magento\Framework\HTTP\PhpEnvironment\RemoteAddress'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->config = $this->getMockBuilder(
            'Magento\Payment\Gateway\ConfigInterface'
        )
            ->getMockForAbstractClass();
        $this->orderRepository = $this->getMockBuilder(
            'Magento\Sales\Api\OrderRepositoryInterface'
        )
            ->getMockForAbstractClass();

        $this->resultMock = $this->getMockBuilder(
            'Magento\Payment\Gateway\Validator\ResultInterface'
        )->getMockForAbstractClass();

        $this->validator = new ResponseValidator(
            $this->resultFactory,
            $this->request,
            $this->remoteAddress,
            $this->config,
            $this->orderRepository
        );
    }

    public function testValidateSuccess()
    {
        $responsePassword = '1234';
        $orderId = '1';
        $storeId = '1';

        $this->request->expects(static::once())
            ->method('isPost')
            ->willReturn(true);
        $this->remoteAddress->expects(static::once())
            ->method('getRemoteHost')
            ->willReturn('subdomain.worldpay.com');
        $this->request->expects(static::any())
            ->method('getPost')
            ->willReturnMap(
                [
                    [null, null, ['request']],
                    [OrderDataBuilder::ORDER_ID, null, $orderId],
                    [OrderDataBuilder::STORE_ID, null, $storeId],
                    [ResponseValidator::RESPONSE_PASSWORD, null, $responsePassword]
                ]
            );
        $this->orderRepository->expects(static::once())
            ->method('get')
            ->with($orderId);
        $this->config->expects(static::once())
            ->method('getValue')
            ->with('response_password', $storeId)
            ->willReturn($responsePassword);

        $this->resultFactory->expects(static::once())
            ->method('create')
            ->with(
                [
                    'isValid' => true,
                    'failsDescription' => []
                ]
            )
            ->willReturn($this->resultMock);

        $this->validator->validate([]);
    }
}
