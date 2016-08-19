<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PersistentHistory\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SetQuotePersistentDataObserver implements ObserverInterface
{
    /**
     * Persistent session
     *
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSession = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Persistent data
     *
     * @var \Magento\PersistentHistory\Helper\Data
     */
    protected $_ePersistentData = null;

    /**
     * Persistent data
     *
     * @var \Magento\Persistent\Helper\Data
     */
    protected $_mPersistentData = null;

    /**
     * Whether set quote to be persistent in workflow
     *
     * @var QuotePersistentPreventFlag
     */
    protected $quotePersistent;

    /**
     * @param \Magento\Persistent\Helper\Session $persistentSession
     * @param \Magento\PersistentHistory\Helper\Data $ePersistentData
     * @param \Magento\Persistent\Helper\Data $mPersistentData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param QuotePersistentPreventFlag $quotePersistent
     */
    public function __construct(
        \Magento\Persistent\Helper\Session $persistentSession,
        \Magento\PersistentHistory\Helper\Data $ePersistentData,
        \Magento\Persistent\Helper\Data $mPersistentData,
        \Magento\Customer\Model\Session $customerSession,
        QuotePersistentPreventFlag $quotePersistent
    ) {
        $this->_persistentSession = $persistentSession;
        $this->_mPersistentData = $mPersistentData;
        $this->_ePersistentData = $ePersistentData;
        $this->_customerSession = $customerSession;
        $this->quotePersistent = $quotePersistent;
    }

    /**
     * Set persistent data into quote
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->_mPersistentData->canProcess($observer) || !$this->_persistentSession->isPersistent()) {
            return;
        }

        /** @var $quote \Magento\Quote\Model\Quote */
        $quote = $observer->getEvent()->getQuote();
        if (!$quote) {
            return;
        }

        /** @var $customerSession \Magento\Customer\Model\Session */
        $customerSession = $this->_customerSession;

        $helper = $this->_ePersistentData;
        if ($helper->isCustomerAndSegmentsPersist() && $this->quotePersistent->getValue()) {
            $customerId = $customerSession->getCustomerId();
            if ($customerId) {
                $quote->setCustomerId($customerId);
            }
            $customerGroupId = $customerSession->getCustomerGroupId();
            if ($customerGroupId) {
                $quote->setCustomerGroupId($customerGroupId);
            }
        }
    }
}
