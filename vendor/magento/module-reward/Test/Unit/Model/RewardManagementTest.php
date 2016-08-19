<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Model;

class RewardManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\RewardManagement
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $importerMock;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->quoteRepositoryMock = $this->getMock('\Magento\Quote\Api\CartRepositoryInterface');
        $this->rewardDataMock = $this->getMock('\Magento\Reward\Helper\Data', [], [], '', false);
        $this->importerMock = $this->getMock('\Magento\Reward\Model\PaymentDataImporter', [], [], '', false);

        $this->model = $objectManager->getObject(
            'Magento\Reward\Model\RewardManagement',
            [
                'quoteRepository' => $this->quoteRepositoryMock,
                'rewardData' => $this->rewardDataMock,
                'importer' => $this->importerMock
            ]
        );
    }

    public function testSetRewards()
    {
        $cartId = 100;
        $this->rewardDataMock->expects($this->once())->method('isEnabledOnFront')->willReturn(true);

        $quoteMock = $this->getMock(
            '\Magento\Quote\Model\Quote',
            ['__wakeup', 'getPayment', 'collectTotals'],
            [],
            '',
            false
        );
        $this->quoteRepositoryMock->expects($this->once())->method('getActive')->with($cartId)->willReturn($quoteMock);
        $this->quoteRepositoryMock->expects($this->once())->method('save')->with($quoteMock);

        $paymentMock = $this->getMock('\Magento\Quote\Model\Quote\Payment', [], [], '', false);

        $quoteMock->expects($this->once())->method('getPayment')->willReturn($paymentMock);
        $quoteMock->expects($this->once())->method('collectTotals')->willReturnSelf();

        $this->importerMock->expects($this->once())
            ->method('import')
            ->with($quoteMock, $paymentMock, true)
            ->willReturnSelf();

        $this->assertTrue($this->model->set($cartId));
    }

    public function testSetRewardsIfDisabledOnFront()
    {
        $this->rewardDataMock->expects($this->once())->method('isEnabledOnFront')->willReturn(false);
        $this->assertFalse($this->model->set(1));
    }
}
