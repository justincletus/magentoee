<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerBalance\Observer;

use Magento\Framework\Event\ObserverInterface;

class CreditmemoDataImportObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Set refund flag to creditmemo based on user input
     * used for event: adminhtml_sales_order_creditmemo_register_before
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();

        $input = $observer->getEvent()->getInput();

        if (isset($input['refund_customerbalance_return']) && isset($input['refund_customerbalance_return_enable'])) {
            $enable = $input['refund_customerbalance_return_enable'];
            $amount = $input['refund_customerbalance_return'];
            if ($enable && is_numeric($amount)) {
                $amount = max(0, min($creditmemo->getBaseCustomerBalanceReturnMax(), $amount));
                if ($amount) {
                    $amount = $this->priceCurrency->round($amount);
                    $creditmemo->setBsCustomerBalTotalRefunded($amount);

                    $amount = $this->priceCurrency->round(
                        $amount * $creditmemo->getOrder()->getBaseToOrderRate()
                    );
                    $creditmemo->setCustomerBalTotalRefunded($amount);
                    //setting flag to make actual refund to customer balance after creditmemo save
                    $creditmemo->setCustomerBalanceRefundFlag(true);
                    //allow online refund
                    $creditmemo->setPaymentRefundDisallowed(false);
                }
            }
        }

        if (isset($input['refund_customerbalance']) && $input['refund_customerbalance']) {
            $creditmemo->setRefundCustomerBalance(true);
        }

        if (isset($input['refund_real_customerbalance']) && $input['refund_real_customerbalance']) {
            $creditmemo->setRefundRealCustomerBalance(true);
            $creditmemo->setPaymentRefundDisallowed(true);
        }

        return $this;
    }
}
