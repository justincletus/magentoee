<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PersistentHistory\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SkipWebsiteRestrictionObserver implements ObserverInterface
{
    /**
     * Persistent session
     *
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSession = null;

    /**
     * @param \Magento\Persistent\Helper\Session $persistentSession
     */
    public function __construct(\Magento\Persistent\Helper\Session $persistentSession)
    {
        $this->_persistentSession = $persistentSession;
    }

    /**
     * Skip website restriction and allow access for persistent customers
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $result = $observer->getEvent()->getResult();
        if ($result->getShouldProceed() && $this->_persistentSession->isPersistent()) {
            $result->setCustomerLoggedIn(true);
        }
    }
}
