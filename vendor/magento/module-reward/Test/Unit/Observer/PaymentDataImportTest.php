<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Observer;

class PaymentDataImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $importerMock;

    /**
     * @var \Magento\Reward\Observer\PaymentDataImport
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager  */
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->rewardDataMock = $this->getMock('\Magento\Reward\Helper\Data', [], [], '', false);
        $this->importerMock = $this->getMock('\Magento\Reward\Model\PaymentDataImporter', [], [], '', false);

        $this->subject = $objectManager->getObject(
            'Magento\Reward\Observer\PaymentDataImport',
            ['rewardData' => $this->rewardDataMock, 'importer' => $this->importerMock]
        );
    }

    public function testPaymentDataImportIfRewardsDisabledOnFront()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->rewardDataMock->expects($this->once())->method('isEnabledOnFront')->will($this->returnValue(false));
        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testPaymentDataImportSuccess()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->rewardDataMock->expects($this->once())->method('isEnabledOnFront')->will($this->returnValue(true));

        $inputMock = $this->getMock('\Magento\Framework\DataObject', ['getUseRewardPoints', '__wakeup'], [], '', false);
        $inputMock->expects($this->once())->method('getUseRewardPoints')->will($this->returnValue(true));
        $quoteMock = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false);

        $paymentMock = $this->getMock('\Magento\Sales\Model\Order\Payment', ['getQuote', '__wakeup'], [], '', false);
        $paymentMock->expects($this->once())->method('getQuote')->will($this->returnValue($quoteMock));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getRule', 'getInput', 'getPayment'], [], '', false);
        $eventMock->expects($this->once())->method('getInput')->will($this->returnValue($inputMock));
        $eventMock->expects($this->once())->method('getPayment')->will($this->returnValue($paymentMock));
        $observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($eventMock));

        $this->importerMock->expects($this->once())
            ->method('import')
            ->with($quoteMock, $inputMock, true)
            ->will($this->returnSelf());

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
