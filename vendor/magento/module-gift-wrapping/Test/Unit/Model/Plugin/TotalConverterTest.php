<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Test\Unit\Model\Plugin;

use Magento\GiftWrapping\Model\Plugin\TotalsConverter;

class TotalConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftWrapping\Model\Plugin\TotalsConverter
     */
    protected $plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $totalsSegExtFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $totalExt;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $totalMock;

    protected function setUp()
    {
        $this->totalExt = $this->getMock(
            '\Magento\Quote\Api\Data\TotalSegmentExtensionInterface',
            [
                'setGwItemIds',
                'setGwOrderId',
                'setGwPrice',
                'setGwBasePrice',
                'setGwItemsPrice',
                'setGwAllowGiftReceipt',
                'setGwAddCard',
                'setGwCardPrice',
                'setGwCardBasePrice',
                'setGwTaxAmount',
                'setGwBaseTaxAmount',
                'setGwItemsTaxAmount',
                'setGwCardTaxAmount',
                'setGwItemsBaseTaxAmount',
                'setGwItemsBasePrice',
                'setGwCardBaseTaxAmount',
                'setGwPriceInclTax',
                'setGwBasePriceInclTax',
                'setGwCardPriceInclTax',
                'setGwCardBasePriceInclTax',
                'setGwItemsPriceInclTax',
                'setGwItemsBasePriceInclTax'
            ],
            [],
            '',
            false
        );

        $this->totalMock = $this->getMock(
            '\Magento\Quote\Model\Quote\Address\Total',
            [
                'getGwItemIds',
                'getGwId',
                'getGwPrice',
                'getgwBasePrice',
                'getGwItemsPrice',
                'getGwItemsBasePrice',
                'getGwAllowGiftReceipt',
                'getGwAddCard',
                'getGwCardPrice',
                'getGwCardBasePrice',
                'getGwTaxAmount',
                'getGwBaseTaxAmount',
                'getGwItemsTaxAmount',
                'getGwItemsBaseTaxAmount',
                'getGwCardTaxAmount',
                'getGwCardBaseTaxAmount',
                'getGwPriceInclTax',
                'getGwBasePriceInclTax',
                'getGwCardPriceInclTax',
                'getGwCardBasePriceInclTax',
                'getGwItemsPriceInclTax',
                'getGwItemsBasePriceInclTax'
            ],
            [],
            '',
            false
        );

        $this->totalsSegExtFactory = $this->getMock(
            '\Magento\Quote\Api\Data\TotalSegmentExtensionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->plugin = new TotalsConverter($this->totalsSegExtFactory);
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testAroundProcess()
    {
        $gwItemIds = [1, 2, 3];
        $gwId = 100;
        $gwPrice = 500;
        $gwBasePrice = 600;
        $gwItemsPrice = 300;
        $gwItemsBasePrice = 700;
        $gwAllowGiftReceipt = true;
        $gwAddCard = false;
        $gwCardPrice = 90;
        $gwCardBasePrice = 80;
        $gwTaxAmount = 800;
        $gwBaseTaxAmount = 1000;
        $gwItemsTaxAmount = 98;
        $gwItemsBaseTaxAmount = 77;
        $gwCardTaxAmount = 67;
        $gwCardBaseTaxAmount = 932;
        $gwPriceInclTax = $gwPrice + $gwTaxAmount;
        $gwBasePriceInclTax = $gwBasePrice + $gwBaseTaxAmount;
        $gwCardPriceInclTax = $gwCardPrice + $gwCardTaxAmount;
        $gwCardBasePriceInclTax = $gwCardBasePrice + $gwCardBaseTaxAmount;
        $gwItemsPriceInclTax = $gwItemsPrice + $gwItemsTaxAmount;
        $gwItemsBasePriceInclTax = $gwItemsBasePrice + $gwItemsBaseTaxAmount;

        $this->totalExt->expects($this->once())->method('setGwItemIds')->with($gwItemIds)->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwOrderId')->with($gwId)->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwPrice')->with($gwPrice)->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwBasePrice')->with($gwBasePrice)->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwItemsPrice')->with($gwItemsPrice)->willReturnSelf();
        $this->totalExt
            ->expects($this->once())
            ->method('setGwAllowGiftReceipt')
            ->with($gwAllowGiftReceipt)->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwAddCard')->with($gwAddCard)->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwCardPrice')->with($gwCardPrice)->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwCardBasePrice')->with($gwCardBasePrice)->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwTaxAmount')->with($gwTaxAmount)->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwBaseTaxAmount')->with($gwBaseTaxAmount)->willReturnSelf();
        $this->totalExt
            ->expects($this->once())
            ->method('setGwItemsTaxAmount')
            ->with($gwItemsTaxAmount)->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwCardTaxAmount')->with($gwCardTaxAmount)->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwItemsBaseTaxAmount')
            ->with($gwItemsBaseTaxAmount)
            ->willReturnSelf();
        $this->totalExt->expects($this->once())
            ->method('setGwItemsBasePrice')
            ->with($gwItemsBasePrice)
            ->willReturnSelf();
        $this->totalExt->expects($this->once())
            ->method('setGwCardBaseTaxAmount')
            ->with($gwCardBaseTaxAmount)
            ->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwPriceInclTax')->with($gwPriceInclTax)->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwBasePriceInclTax')->with($gwBasePriceInclTax)
            ->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwCardPriceInclTax')->with($gwCardPriceInclTax)
            ->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwCardBasePriceInclTax')->with($gwCardBasePriceInclTax)
            ->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwItemsPriceInclTax')->with($gwItemsPriceInclTax)
            ->willReturnSelf();
        $this->totalExt->expects($this->once())->method('setGwItemsBasePriceInclTax')->with($gwItemsBasePriceInclTax)
            ->willReturnSelf();

        $this->totalsSegExtFactory->expects($this->once())->method('create')->willReturn($this->totalExt);
        $totalSegments = $this->getMock('\Magento\Quote\Api\Data\TotalSegmentInterface', [], [], '', false);
        $totalSegments->expects($this->once())->method('setExtensionAttributes');

        $proceed = function () use (&$totalSegments) {
            return ['giftwrapping' => $totalSegments];
        };

        $this->totalMock->expects($this->once())->method('getGwItemIds')->willReturn($gwItemIds);
        $this->totalMock->expects($this->once())->method('getGwId')->willReturn($gwId);
        $this->totalMock->expects($this->once())->method('getGwPrice')->willReturn($gwPrice);
        $this->totalMock->expects($this->once())->method('getgwBasePrice')->willReturn($gwBasePrice);
        $this->totalMock->expects($this->once())->method('getGwItemsPrice')->willReturn($gwItemsPrice);
        $this->totalMock->expects($this->once())->method('getGwItemsBasePrice')->willReturn($gwItemsBasePrice);
        $this->totalMock->expects($this->once())->method('getGwAllowGiftReceipt')->willReturn($gwAllowGiftReceipt);
        $this->totalMock->expects($this->once())->method('getGwAddCard')->willReturn($gwAddCard);
        $this->totalMock->expects($this->once())->method('getGwCardPrice')->willReturn($gwCardPrice);
        $this->totalMock->expects($this->once())->method('getGwCardBasePrice')->willReturn($gwCardBasePrice);
        $this->totalMock->expects($this->once())->method('getGwTaxAmount')->willReturn($gwTaxAmount);
        $this->totalMock->expects($this->once())->method('getGwBaseTaxAmount')->willReturn($gwBaseTaxAmount);
        $this->totalMock->expects($this->once())->method('getGwItemsTaxAmount')->willReturn($gwItemsTaxAmount);
        $this->totalMock->expects($this->once())->method('getGwItemsBaseTaxAmount')->willReturn($gwItemsBaseTaxAmount);
        $this->totalMock->expects($this->once())->method('getGwCardTaxAmount')->willReturn($gwCardTaxAmount);
        $this->totalMock->expects($this->once())->method('getGwCardBaseTaxAmount')->willReturn($gwCardBaseTaxAmount);
        $this->totalMock->expects($this->once())->method('getGwPriceInclTax')->willReturn($gwPriceInclTax);
        $this->totalMock->expects($this->once())->method('getGwBasePriceInclTax')->willReturn($gwBasePriceInclTax);
        $this->totalMock->expects($this->once())->method('getGwCardPriceInclTax')->willReturn($gwCardPriceInclTax);
        $this->totalMock->expects($this->once())->method('getGwCardBasePriceInclTax')
            ->willReturn($gwCardBasePriceInclTax);
        $this->totalMock->expects($this->once())->method('getGwItemsPriceInclTax')->willReturn($gwItemsPriceInclTax);
        $this->totalMock->expects($this->once())->method('getGwItemsBasePriceInclTax')
            ->willReturn($gwItemsBasePriceInclTax);

        $this->plugin->aroundProcess(
            $this->getMock('\Magento\Quote\Model\Cart\TotalsConverter', [], [], '', false),
            $proceed,
            ['giftwrapping' => $this->totalMock]
        );
    }
}
