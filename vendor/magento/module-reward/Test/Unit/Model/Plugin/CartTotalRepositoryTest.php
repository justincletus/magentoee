<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Model\Plugin;

class CartTotalRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Plugin\CartTotalRepository
     */
    protected $model;

    /**
     * @var \Magento\Quote\Model\Quote | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quote;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Quote\Api\Data\TotalsInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $totals;

    /**
     * @var \Magento\Quote\Api\Data\TotalsExtensionFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $totalsExtensionFactory;

    /**
     * @var \Magento\Quote\Api\Data\TotalsExtensionInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $totalsExtension;

    /**
     * @var \Magento\Quote\Model\Cart\CartTotalRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    protected function setUp()
    {
        $this->quote = $this->getMockBuilder('Magento\Quote\Model\Quote')
            ->disableOriginalConstructor()
            ->setMethods([
                'getRewardPointsBalance',
                'getRewardCurrencyAmount',
                'getBaseRewardCurrencyAmount',
            ])
            ->getMock();

        $this->quoteRepository = $this->getMock('\Magento\Quote\Api\CartRepositoryInterface');
        $this->totals = $this->getMockBuilder('Magento\Quote\Api\Data\TotalsInterface')
            ->getMock();

        $this->totalsExtension = $this->getMockBuilder('Magento\Quote\Api\Data\TotalsExtensionInterface')
            ->setMethods(['setRewardPointsBalance', 'setRewardCurrencyAmount', 'setBaseRewardCurrencyAmount'])
            ->getMockForAbstractClass();

        $this->totalsExtensionFactory = $this->getMockBuilder('Magento\Quote\Api\Data\TotalsExtensionFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMockForAbstractClass();
        $this->totalsExtensionFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->totalsExtension);

        $this->subject = $this->getMockBuilder('Magento\Quote\Model\Cart\CartTotalRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = new \Magento\Reward\Model\Plugin\CartTotalRepository(
            $this->quoteRepository,
            $this->totalsExtensionFactory
        );
    }

    public function testAroundGet()
    {
        $cartId = 1;
        $rewardPointsBalance = 1.;
        $rewardCurrencyAmount = 1.;
        $baseRewardCurrencyAmount = 1.;

        $this->quote->expects($this->once())
            ->method('getRewardPointsBalance')
            ->willReturn($rewardPointsBalance);
        $this->quote->expects($this->once())
            ->method('getRewardCurrencyAmount')
            ->willReturn($rewardCurrencyAmount);
        $this->quote->expects($this->once())
            ->method('getBaseRewardCurrencyAmount')
            ->willReturn($baseRewardCurrencyAmount);

        $this->quoteRepository->expects($this->once())
            ->method('getActive')
            ->willReturn($this->quote);

        $this->totalsExtension->expects($this->once())
            ->method('setRewardPointsBalance')
            ->willReturn($rewardPointsBalance);
        $this->totalsExtension->expects($this->once())
            ->method('setRewardCurrencyAmount')
            ->willReturn($rewardCurrencyAmount);
        $this->totalsExtension->expects($this->once())
            ->method('setBaseRewardCurrencyAmount')
            ->willReturn($baseRewardCurrencyAmount);

        $this->totals->expects($this->any())
            ->method('getExtensionAttributes')
            ->willReturn($this->totalsExtension);
        $this->totals->expects($this->once())
            ->method('setExtensionAttributes')
            ->with($this->totalsExtension)
            ->willReturnSelf();

        $closure = function () {
            return $this->totals;
        };

        $result = $this->model->aroundGet($this->subject, $closure, $cartId);
        $this->assertEquals($this->totalsExtension, $result->getExtensionAttributes());
    }

    public function testAroundGetCreateExtensionAttributes()
    {
        $cartId = 1;
        $rewardPointsBalance = 1.;
        $rewardCurrencyAmount = 1.;
        $baseRewardCurrencyAmount = 1.;

        $this->quote->expects($this->once())
            ->method('getRewardPointsBalance')
            ->willReturn($rewardPointsBalance);
        $this->quote->expects($this->once())
            ->method('getRewardCurrencyAmount')
            ->willReturn($rewardCurrencyAmount);
        $this->quote->expects($this->once())
            ->method('getBaseRewardCurrencyAmount')
            ->willReturn($baseRewardCurrencyAmount);

        $this->quoteRepository->expects($this->once())
            ->method('getActive')
            ->willReturn($this->quote);

        $this->totalsExtension->expects($this->once())
            ->method('setRewardPointsBalance')
            ->willReturn($rewardPointsBalance);
        $this->totalsExtension->expects($this->once())
            ->method('setRewardCurrencyAmount')
            ->willReturn($rewardCurrencyAmount);
        $this->totalsExtension->expects($this->once())
            ->method('setBaseRewardCurrencyAmount')
            ->willReturn($baseRewardCurrencyAmount);

        $this->totals->expects($this->any())
            ->method('getExtensionAttributes')
            ->willReturn(null);
        $this->totals->expects($this->once())
            ->method('setExtensionAttributes')
            ->with($this->totalsExtension)
            ->willReturnSelf();

        $closure = function () {
            return $this->totals;
        };

        $result = $this->model->aroundGet($this->subject, $closure, $cartId);
        $this->assertNull($result->getExtensionAttributes());
    }
}
