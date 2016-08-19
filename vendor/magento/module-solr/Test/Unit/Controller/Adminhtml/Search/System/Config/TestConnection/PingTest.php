<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Solr\Test\Unit\Controller\Adminhtml\Search\System\Config\TestConnection;

use Magento\Solr\Helper\ClientOptionsInterface;
use Magento\Solr\Controller\Adminhtml\Search\System\Config\TestConnection\Ping;
use Magento\Solr\Model\Client\Solarium;

class PingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var Solarium|\PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var ClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $clientHelper;

    /**
     * @var \Magento\Framework\Controller\Result\Json|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultJson;

    /**
     * @var Ping
     */
    private $controller;

    /**
     * Setup test function
     *
     * @return void
     */
    protected function setUp()
    {
        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->requestMock = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $responseMock = $this->getMock('Magento\Framework\App\Response\Http', [], [], '', false);

        $context = $this->getMock(
            'Magento\Backend\App\Action\Context',
            ['getRequest', 'getResponse', 'getMessageManager', 'getSession'],
            $helper->getConstructArguments(
                'Magento\Backend\App\Action\Context',
                [
                    'request' => $this->requestMock
                ]
            )
        );
        $context->expects($this->once())->method('getRequest')->will($this->returnValue($this->requestMock));
        $context->expects($this->once())->method('getResponse')->will($this->returnValue($responseMock));

        $this->client = $this->getMockBuilder('\Magento\Solr\Model\Client\Solarium')
        ->disableOriginalConstructor()
        ->setMethods(['ping'])
        ->getMock();

        $clientFactory = $this->getMockBuilder('\Magento\Solr\Model\Client\FactoryInterface')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $clientFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->client);

        $this->clientHelper = $this->getMockBuilder(ClientOptionsInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'prepareClientOptions',
                    'getSolrSupportedLanguages',
                    'getLanguageCodeByLocaleCode',
                    'getLanguageSuffix'
                ]
            )
            ->getMock();

        $this->resultJson = $this->getMockBuilder('Magento\Framework\Controller\Result\Json')
            ->disableOriginalConstructor()
            ->getMock();

        $resultJsonFactory = $this->getMockBuilder('Magento\Framework\Controller\Result\JsonFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $resultJsonFactory->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->resultJson);

        $this->controller = new Ping($context, $clientFactory, $this->clientHelper, $resultJsonFactory);
    }

    /**
     * @dataProvider emptyParamDataProvider
     *
     * @param string $host
     * @param string $port
     * @param string $path
     * @return void
     */
    public function testExecuteEmptyParam($host, $port, $path)
    {
        $expected = [
            'success' => false,
            'error_message' => __('Please fill in Hostname, Port, Path')
        ];
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->willReturnOnConsecutiveCalls($host, $port, $path);
        $this->resultJson->expects($this->once())->method('setData')->with($expected);
        $this->controller->execute();
    }

    /**
     * @return array
     */
    public function emptyParamDataProvider()
    {
        return [
            ['', '', ''],
            ['', '8983', 'solr'],
            ['localhost', '', 'solr'],
            ['localhost', '8983', ''],
        ];
    }

    /**
     * @return void
     */
    public function testExecute()
    {
        $expected = [
            'success' => true,
            'error_message' => ''
        ];
        $params = ['hostname' => 'localhost', 'port' => '8983', 'path' => 'solr', 'timeout' => 0];
        $this->clientHelper->expects($this->once())
            ->method('prepareClientOptions')
            ->with($params)
            ->willReturnArgument(0);
        $this->client->expects($this->once())
            ->method('ping')
            ->willReturn(['status' => 'OK']);
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->willReturnOnConsecutiveCalls($params['hostname'], $params['port'], $params['path']);
        $this->resultJson->expects($this->once())->method('setData')->with($expected);
        $this->controller->execute();
    }

    /**
     * @return void
     */
    public function testExecuteFailedPing()
    {
        $expected = [
            'success' => false,
            'error_message' => ''
        ];
        $params = ['hostname' => 'localhost', 'port' => '8983', 'path' => 'solr', 'timeout' => 0];
        $this->clientHelper->expects($this->once())
            ->method('prepareClientOptions')
            ->with($params)
            ->willReturnArgument(0);
        $this->client->expects($this->once())
            ->method('ping')
            ->willReturn(['status' => 'notOK']);
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->willReturnOnConsecutiveCalls($params['hostname'], $params['port'], $params['path']);
        $this->resultJson->expects($this->once())->method('setData')->with($expected);
        $this->controller->execute();
    }

    /**
     * @return void
     */
    public function testExecuteException()
    {
        $expected = [
            'success' => false,
            'error_message' => __('Something went wrong')
        ];
        $params = ['hostname' => 'localhost', 'port' => '8983', 'path' => 'solr', 'timeout' => 0];
        $this->clientHelper->expects($this->once())
            ->method('prepareClientOptions')
            ->with($params)
            ->willReturnArgument(0);
        $this->client->expects($this->once())
            ->method('ping')
            ->willThrowException(new \Exception('<p>Something went wrong<\p>'));
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->willReturnOnConsecutiveCalls($params['hostname'], $params['port'], $params['path']);
        $this->resultJson->expects($this->once())->method('setData')->with($expected);
        $this->controller->execute();
    }
}
