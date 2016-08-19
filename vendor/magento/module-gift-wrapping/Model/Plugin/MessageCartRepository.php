<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Model\Plugin;

use Magento\GiftMessage\Api\CartRepositoryInterface;
use Magento\GiftMessage\Api\Data\MessageInterface;
use Magento\GiftWrapping\Helper\Data;
use Magento\GiftWrapping\Model\WrappingFactory;

class MessageCartRepository
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var WrappingFactory
     */
    protected $wrappingFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param WrappingFactory $wrappingFactory
     * @param Data $helper
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        WrappingFactory $wrappingFactory,
        Data $helper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->wrappingFactory = $wrappingFactory;
        $this->helper = $helper;
    }

    /**
     * Set gift wrapping from message for cart
     *
     * @param CartRepositoryInterface $subject
     * @param callable $proceed
     * @param int $cartId
     * @param MessageInterface $giftMessage
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function aroundSave(
        CartRepositoryInterface $subject,
        \Closure $proceed,
        $cartId,
        MessageInterface $giftMessage
    ) {
        $proceed($cartId, $giftMessage);

        $quote = $this->quoteRepository->getActive($cartId);

        $wrappingInfo = [];
        $extensionAttributes = $giftMessage->getExtensionAttributes();

        if ($extensionAttributes && $this->helper->isGiftWrappingAvailableForOrder()) {
            $wrappingId = $extensionAttributes->getWrappingId();
            $wrapping = $this->wrappingFactory->create()->load($wrappingId);
            $wrappingInfo['gw_id'] = $wrapping->getId();
        }

        if ($extensionAttributes && $this->helper->allowGiftReceipt()) {
            $allowGiftReceipt = $extensionAttributes->getWrappingAllowGiftReceipt();
            if ($allowGiftReceipt !== null) {
                $wrappingInfo['gw_allow_gift_receipt'] = $allowGiftReceipt;
            }
        }

        if ($extensionAttributes && $this->helper->allowPrintedCard()) {
            $addPrintedCard = $extensionAttributes->getWrappingAddPrintedCard();
            if ($addPrintedCard !== null) {
                $wrappingInfo['gw_add_card'] = $addPrintedCard;
            }
        }

        if (count($wrappingInfo)) {
            if ($quote->getShippingAddress()) {
                $quote->getShippingAddress()->addData($wrappingInfo);
            }
            $quote->addData($wrappingInfo)->save();
        }

        return true;
    }
}
