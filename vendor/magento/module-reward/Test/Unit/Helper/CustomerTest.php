<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Helper;

class CustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Magento\Reward\Helper\Customer
     */
    protected $subject;

    protected function setUp()
    {
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $contextMock = $this->getMock('\Magento\Framework\App\Helper\Context', [], [], '', false);
        $this->storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);

        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->subject = $objectManagerHelper->getObject(
            '\Magento\Reward\Helper\Customer',
            ['storeManager' => $this->storeManagerMock, 'context' => $contextMock]
        );
    }

    public function testGetUnsubscribeUrlIfNotificationDisabled()
    {
        $storeId = 100;
        $url = 'unsubscribe_url';
        $params = ['store_id' => $storeId];

        $this->storeMock->expects($this->once())
            ->method('getUrl')
            ->with('magento_reward/customer/unsubscribe/', $params)
            ->willReturn($url);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')->with($storeId)->willReturn($this->storeMock);
        $this->assertEquals($url, $this->subject->getUnsubscribeUrl(false, $storeId));
    }

    public function testGetUnsubscribeUrlIfNotificationEnabled()
    {
        $storeId = 100;
        $url = 'unsubscribe_url';
        $params = ['store_id' => $storeId, 'notification' => true];

        $this->storeMock->expects($this->once())
            ->method('getUrl')
            ->with('magento_reward/customer/unsubscribe/', $params)
            ->willReturn($url);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')->with($storeId)->willReturn($this->storeMock);
        $this->assertEquals($url, $this->subject->getUnsubscribeUrl(true, $storeId));
    }

    public function testGetUnsubscribeUrlIfStoreIdNotSet()
    {
        $url = 'unsubscribe_url';
        $params = ['notification' => true];

        $this->storeMock->expects($this->once())
            ->method('getUrl')
            ->with('magento_reward/customer/unsubscribe/', $params)
            ->willReturn($url);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')->with(null)->willReturn($this->storeMock);
        $this->assertEquals($url, $this->subject->getUnsubscribeUrl(true));
    }
}
