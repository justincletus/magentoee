<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Test\Unit\Observer;

class SalesQuoteAfterLoadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerCustomAttributes\Observer\SalesQuoteAfterLoad
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

        $this->observer = new \Magento\CustomerCustomAttributes\Observer\SalesQuoteAfterLoad($this->quoteFactory);
    }

    public function testSalesQuoteAfterLoad()
    {
        $quoteId = 1;
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getQuote'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->setMethods(['getId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $quote = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Quote')
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel->expects($this->once())->method('getId')->will($this->returnValue($quoteId));
        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getQuote')->will($this->returnValue($dataModel));
        $quote->expects($this->once())->method('load')->with($quoteId)->will($this->returnSelf());
        $quote->expects($this->once())->method('attachAttributeData')->with($dataModel)->will($this->returnSelf());
        $this->quoteFactory->expects($this->once())->method('create')->will($this->returnValue($quote));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Observer\SalesQuoteAfterLoad',
            $this->observer->execute($observer)
        );
    }
}
