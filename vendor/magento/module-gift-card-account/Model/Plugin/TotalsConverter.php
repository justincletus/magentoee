<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCardAccount\Model\Plugin;

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
        $this->code = 'giftcardaccount';
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

        /** @var \Magento\Quote\Api\Data\TotalSegmentExtensionInterface $totalSegmentExtension */
        $totalSegmentExtension = $this->totalSegmentExtensionFactory->create();
        $totalSegmentExtension->setGiftCards(json_encode($addressTotals[$this->code]->getGiftCards()));
        $totalSegments[$this->code]->setExtensionAttributes($totalSegmentExtension);

        return $totalSegments;
    }
}
