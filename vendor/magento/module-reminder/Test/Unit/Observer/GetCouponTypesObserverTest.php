<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reminder\Test\Unit\Observer;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class GetCouponTypesObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reminder\Observer\GetCouponTypesObserver
     */
    private $model;

    /**
     * @var \Magento\Framework\Event\Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventObserver;

    /**
     * @return void
     */
    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->eventObserver = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->setMethods(['getCollection', 'getRule', 'getForm', 'getEvent'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $helper->getObject(
            'Magento\Reminder\Observer\GetCouponTypesObserver'
        );
    }

    /**
     * @return void
     */
    public function testGetCouponTypes()
    {
        $transportMock = $this->getMockBuilder('Magento\Framework\Mail\Transport')
            ->setMethods(['setIsCouponTypeAutoVisible'])
            ->disableOriginalConstructor()
            ->getMock();
        $transportMock->expects($this->once())->method('setIsCouponTypeAutoVisible')->with(true);

        $eventMock = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getTransport'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())->method('getTransport')->willReturn($transportMock);

        $this->eventObserver->expects($this->once())->method('getEvent')->willReturn($eventMock);
        $this->model->execute($this->eventObserver);
    }
}
