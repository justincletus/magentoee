<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Observer;

use Magento\Framework\Event\ObserverInterface;

class PaymentDataImport implements ObserverInterface
{
    /**
     * Reward helper
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData;

    /**
     * @var \Magento\Reward\Model\PaymentDataImporter
     */
    protected $importer;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Reward\Model\PaymentDataImporter $importer
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Reward\Model\PaymentDataImporter $importer
    ) {
        $this->_rewardData = $rewardData;
        $this->importer = $importer;
    }

    /**
     * Payment data import in checkout process
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_rewardData->isEnabledOnFront()) {
            $input = $observer->getEvent()->getInput();
            /* @var $quote \Magento\Quote\Model\Quote */
            $quote = $observer->getEvent()->getPayment()->getQuote();
            $this->importer->import($quote, $input, $input->getUseRewardPoints());
        }
        return $this;
    }
}
