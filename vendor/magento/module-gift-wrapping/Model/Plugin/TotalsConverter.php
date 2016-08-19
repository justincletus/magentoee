<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Model\Plugin;

use Magento\Quote\Api\Data\TotalSegmentExtensionFactory;

class TotalsConverter
{
    /**
     * @var TotalSegmentExtensionFactory
     */
    protected $totalSegmentExtensionFactory;

    /**
     * @var string
     */
    protected $code;

    /**
     * @param TotalSegmentExtensionFactory $totalSegmentExtensionFactory
     */
    public function __construct(
        TotalSegmentExtensionFactory $totalSegmentExtensionFactory
    ) {
        $this->totalSegmentExtensionFactory = $totalSegmentExtensionFactory;
        $this->code = 'giftwrapping';
    }

    /**
     * @param \Magento\Quote\Model\Cart\TotalsConverter $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Address\Total[] $addressTotals
     * @return \Magento\Quote\Api\Data\TotalSegmentInterface[]
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundProcess(
        \Magento\Quote\Model\Cart\TotalsConverter $subject,
        \Closure $proceed,
        array $addressTotals = []
    ) {
        /** @var \Magento\Quote\Api\Data\TotalSegmentInterface[] $totals */
        $totalSegments = $proceed($addressTotals);
        if (!isset($addressTotals[$this->code])) {
            return $totalSegments;
        }

        $total = $addressTotals[$this->code];
        /** @var \Magento\Quote\Api\Data\TotalSegmentExtensionInterface $totalSegmentExtension */
        $totalSegmentExtension = $this->totalSegmentExtensionFactory->create();
        $totalSegmentExtension->setGwItemIds($total->getGwItemIds());
        $totalSegmentExtension->setGwOrderId($total->getGwId());
        
        $totalSegmentExtension->setGwPrice($total->getGwPrice());
        $totalSegmentExtension->setGwBasePrice($total->getgwBasePrice());
        $totalSegmentExtension->setGwItemsPrice($total->getGwItemsPrice());
        $totalSegmentExtension->setGwItemsBasePrice($total->getGwItemsBasePrice());

        $totalSegmentExtension->setGwAllowGiftReceipt($total->getGwAllowGiftReceipt());
        $totalSegmentExtension->setGwAddCard($total->getGwAddCard());

        $totalSegmentExtension->setGwCardPrice($total->getGwCardPrice());
        $totalSegmentExtension->setGwCardBasePrice($total->getGwCardBasePrice());
        $totalSegmentExtension->setGwTaxAmount($total->getGwTaxAmount());
        $totalSegmentExtension->setGwBaseTaxAmount($total->getGwBaseTaxAmount());
        $totalSegmentExtension->setGwItemsTaxAmount($total->getGwItemsTaxAmount());
        $totalSegmentExtension->setGwItemsBaseTaxAmount($total->getGwItemsBaseTaxAmount());
        $totalSegmentExtension->setGwCardTaxAmount($total->getGwCardTaxAmount());
        $totalSegmentExtension->setGwCardBaseTaxAmount($total->getGwCardBaseTaxAmount());

        $totalSegmentExtension->setGwPriceInclTax($total->getGwPriceInclTax());
        $totalSegmentExtension->setGwBasePriceInclTax($total->getGwBasePriceInclTax());
        $totalSegmentExtension->setGwCardPriceInclTax($total->getGwCardPriceInclTax());
        $totalSegmentExtension->setGwCardBasePriceInclTax($total->getGwCardBasePriceInclTax());
        $totalSegmentExtension->setGwItemsPriceInclTax($total->getGwItemsPriceInclTax());
        $totalSegmentExtension->setGwItemsBasePriceInclTax($total->getGwItemsBasePriceInclTax());

        $totalSegments[$this->code]->setExtensionAttributes($totalSegmentExtension);

        return $totalSegments;
    }
}
