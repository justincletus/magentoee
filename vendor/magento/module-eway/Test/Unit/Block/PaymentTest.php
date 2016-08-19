<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Eway\Test\Unit\Block;

use Magento\Eway\Block\Payment;
use Magento\Framework\App\CacheInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Eway\Model\Ui\Direct\ConfigProvider;
use Magento\Framework\Session\SidResolverInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Eway\Model\Adminhtml\Source\ConnectionType;

/**
 * Class PaymentTest
 *
 * @see \Magento\Eway\Block\Payment
 */
class PaymentTest extends \PHPUnit_Framework_TestCase
{
    const MODULE_NAME = 'module_name';

    /**
     * @var Payment
     */
    private $payment;

    /**
     * @var ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventManagerMock;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * @var CacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cacheMock;

    /**
     * @var StateInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cacheStateMock;

    /**
     * @var SidResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sidResolverMock;

    /**
     * @var SessionManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp()
    {
        $this->contextMock = $this->getMockBuilder('Magento\Framework\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->configMock = $this->getMockBuilder('Magento\Payment\Gateway\ConfigInterface')
            ->getMockForAbstractClass();
        $this->eventManagerMock = $this->getMockBuilder('Magento\Framework\Event\ManagerInterface')
            ->getMockForAbstractClass();
        $this->scopeConfigMock = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
            ->getMockForAbstractClass();
        $this->cacheMock = $this->getMockBuilder('Magento\Framework\App\CacheInterface')
            ->getMockForAbstractClass();
        $this->cacheStateMock = $this->getMockBuilder('Magento\Framework\App\Cache\StateInterface')
            ->getMockForAbstractClass();
        $this->sidResolverMock = $this->getMockBuilder('Magento\Framework\Session\SidResolverInterface')
            ->getMockForAbstractClass();
        $this->sessionMock = $this->getMockBuilder('Magento\Framework\Session\SessionManagerInterface')
            ->getMockForAbstractClass();

        $this->contextMock->expects($this->once())
            ->method('getEventManager')
            ->willReturn($this->eventManagerMock);
        $this->contextMock->expects($this->once())
            ->method('getScopeConfig')
            ->willReturn($this->scopeConfigMock);
        $this->contextMock->expects($this->once())
            ->method('getCache')
            ->willReturn($this->cacheMock);
        $this->contextMock->expects($this->once())
            ->method('getCacheState')
            ->willReturn($this->cacheStateMock);
        $this->contextMock->expects($this->once())
            ->method('getSidResolver')
            ->willReturn($this->sidResolverMock);
        $this->contextMock->expects($this->once())
            ->method('getSession')
            ->willReturn($this->sessionMock);

        $this->payment = new Payment(
            $this->contextMock,
            $this->configMock,
            [
                'module_name' => self::MODULE_NAME,
                'cache_lifetime' => 999,
                'cache_key' => 'test-cache-key'
            ]
        );
    }

    /**
     * Run test for getPaymentConfig method
     *
     * @param string $expect
     * @param array $encryptionKey
     * @return void
     *
     * @dataProvider dataProvideTestGetPaymentConfig
     */
    public function testGetPaymentConfig($expect, array $encryptionKey)
    {
        $this->configMock->expects($this->exactly(4))
            ->method('getValue')
            ->willReturnMap(
                [
                    ['crypt_script', null, 'test-crypt-script'],
                    ['sandbox_flag', null, $encryptionKey['flag']],
                    [$encryptionKey['key'], null, $encryptionKey['value']]
                ]
            );

        $this->assertEquals($expect, $this->payment->getPaymentConfig());
    }

    /**
     * @return array
     */
    public function dataProvideTestGetPaymentConfig()
    {
        return [
            [
                'expect' => '{"code":"eway","cryptUrl":"test-crypt-script","encryptKey":'
                    . '"test/sandbox/encryption/key","endpoint":"Sandbox"}',
                'encryptionKey' => [
                    'flag' => 1,
                    'key' => 'sandbox_encryption_key',
                    'value' => 'test/sandbox/encryption/key'
                ],
            ],
            [
                'expect' => '{"code":"eway","cryptUrl":"test-crypt-script","encryptKey":'
                    . '"test/live/encryption/key","endpoint":"Production"}',
                'encryptionKey' => [
                    'flag' => 0,
                    'key' => 'live_encryption_key',
                    'value' => 'test/live/encryption/key'
                ],
            ]
        ];
    }

    /**
     * Run test for toHtml method (not empty)
     *
     * @return void
     */
    public function testToHtmlNotEmpty()
    {
        $this->configMock->expects($this->once())
            ->method('getValue')
            ->with('connection_type')
            ->willReturn(ConnectionType::CONNECTION_TYPE_DIRECT);

        $this->toHtmlFlow($this->once());

        $this->loadCacheFlow($this->once());

        $this->assertNotEmpty($this->payment->toHtml());
    }

    /**
     * Run test for toHtml method (empty)
     *
     * @return void
     */
    public function testToHtmlEmpty()
    {
        $this->configMock->expects($this->once())
            ->method('getValue')
            ->with('connection_type')
            ->willReturn(ConnectionType::CONNECTION_TYPE_SHARED);

        $this->toHtmlFlow($this->never());

        $this->loadCacheFlow($this->never());

        $this->assertEmpty($this->payment->toHtml());
    }

    /**
     * Run test for toHtml method (bad value)
     *
     * @return void
     */
    public function testToHtmlBadValue()
    {
        $this->configMock->expects($this->once())
            ->method('getValue')
            ->with('connection_type')
            ->willReturn('bade-value');

        $this->toHtmlFlow($this->never());

        $this->assertEmpty($this->payment->toHtml());
    }

    /**
     * @param \PHPUnit_Framework_MockObject_Matcher_InvokedCount $expects
     */
    private function toHtmlFlow($expects)
    {
        $this->eventManagerMock->expects(clone $expects)
            ->method('dispatch')
            ->with('view_block_abstract_to_html_before', ['block' => $this->payment]);

        $this->cacheStateMock->expects(clone $expects)
            ->method('isEnabled')
            ->with(Payment::CACHE_GROUP)
            ->willReturn(true);
    }

    /**
     * @param \PHPUnit_Framework_MockObject_Matcher_InvokedCount $expects
     */
    private function loadCacheFlow($expects)
    {
        $this->cacheMock->expects(clone $expects)
            ->method('load')
            ->willReturn('cache-data');

        $this->sidResolverMock->expects(clone $expects)
            ->method('getSessionIdQueryParam')
            ->with($this->sessionMock)
            ->willReturn('test-param');

        $this->sessionMock->expects(clone $expects)
            ->method('getSessionId')
            ->willReturn('session-id');
    }

    /**
     * Run test for getCode method
     *
     * @return void
     */
    public function testGetCode()
    {
        $this->assertEquals(ConfigProvider::EWAY_CODE, $this->payment->getCode());
    }
}
