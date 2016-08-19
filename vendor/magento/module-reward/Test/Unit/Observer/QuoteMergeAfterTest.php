<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Observer;

class QuoteMergeAfterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Observer\QuoteMergeAfter
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->subject = $objectManager->getObject('Magento\Reward\Observer\QuoteMergeAfter');
    }

    public function testSetFlagToResetRewardPoints()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $quoteMock = $this->getMock(
            '\Magento\Quote\Model\Quote',
            ['setUseRewardPoints', '__wakeup'],
            [],
            '',
            false
        );
        $quoteMock->expects($this->once())
            ->method('setUseRewardPoints')
            ->with(true)
            ->will($this->returnSelf());

        $sourceMock = $this->getMock('\Magento\Framework\DataObject', ['getUseRewardPoints'], [], '', false);
        $sourceMock->expects($this->exactly(2))->method('getUseRewardPoints')->will($this->returnValue(true));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getQuote', 'getSource'], [], '', false);
        $eventMock->expects($this->once())->method('getQuote')->will($this->returnValue($quoteMock));
        $eventMock->expects($this->once())->method('getSource')->will($this->returnValue($sourceMock));
        $observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($eventMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testSetFlagToResetRewardPointsIfRewardPointsIsNull()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $quoteMock = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false);

        $sourceMock = $this->getMock('\Magento\Framework\DataObject', ['getUseRewardPoints'], [], '', false);
        $sourceMock->expects($this->once())->method('getUseRewardPoints')->will($this->returnValue(false));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getQuote', 'getSource'], [], '', false);
        $eventMock->expects($this->once())->method('getQuote')->will($this->returnValue($quoteMock));
        $eventMock->expects($this->once())->method('getSource')->will($this->returnValue($sourceMock));
        $observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($eventMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
