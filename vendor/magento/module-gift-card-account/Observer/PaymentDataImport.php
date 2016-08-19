<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardAccount\Observer;

use Magento\Framework\Event\ObserverInterface;

class PaymentDataImport implements ObserverInterface
{
    /**
     * Gift card account data
     *
     * @var \Magento\GiftCardAccount\Helper\Data
     */
    protected $giftCAHelper = null;

    /**
     * Gift card account giftcardaccount
     *
     * @var \Magento\GiftCardAccount\Model\GiftcardaccountFactory
     */
    protected $giftCAFactory = null;

    /**
     * Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager = null;

    /**
     * @param \Magento\GiftCardAccount\Helper\Data $giftCAHelper
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCAFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\GiftCardAccount\Helper\Data $giftCAHelper,
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCAFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->giftCAHelper = $giftCAHelper;
        $this->giftCAFactory = $giftCAFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Set flag that giftcard applied on payment step in checkout process
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $quote \Magento\Quote\Model\Quote */
        $quote = $observer->getEvent()->getPayment()->getQuote();
        if (!$quote || !$quote->getCustomerId()) {
            return $this;
        }
        /* Gift cards validation */
        $cards = $this->giftCAHelper->getCards($quote);
        $website = $this->storeManager->getStore($quote->getStoreId())->getWebsite();
        foreach ($cards as $one) {
            $this->giftCAFactory->create()
                ->loadByCode($one[\Magento\GiftCardAccount\Model\Giftcardaccount::CODE])->isValid(true, true, $website);
        }

        if ((double)$quote->getBaseGiftCardsAmountUsed()) {
            $quote->setGiftCardAccountApplied(true);
            $input = $observer->getEvent()->getInput();
            if (!$input->getMethod()) {
                $input->setMethod('free');
            }
        }
        return $this;
    }
}
