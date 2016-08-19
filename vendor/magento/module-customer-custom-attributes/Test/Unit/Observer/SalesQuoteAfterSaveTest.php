<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Test\Unit\Observer;

class SalesQuoteAfterSaveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerCustomAttributes\Observer\SalesQuoteAfterSave
     */
    protected $observer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteFactory;

    public function setUp()
    {
        $this->quoteFactory = $this->getMockBuilder(
            'Magento\CustomerCustomAttributes\Model\Sales\QuoteFactory'
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();

        $this->observer = new \Magento\CustomerCustomAttributes\Observer\SalesQuoteAfterSave($this->quoteFactory);
    }

    public function testSalesQuoteAfterSave()
    {
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getQuote'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->setMethods(['__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $quote = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Quote')
            ->disableOriginalConstructor()
            ->getMock();

        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getQuote')->will($this->returnValue($dataModel));
        $quote->expects($this->once())->method('saveAttributeData')->with($dataModel)->will($this->returnSelf());
        $this->quoteFactory->expects($this->once())->method('create')->will($this->returnValue($quote));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Observer\SalesQuoteAfterSave',
            $this->observer->execute($observer)
        );
    }
}
