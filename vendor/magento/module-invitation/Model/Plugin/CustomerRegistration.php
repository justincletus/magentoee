<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Invitation\Model\Plugin;

class CustomerRegistration
{
    /**
     * @var \Magento\Invitation\Model\Config
     */
    protected $_invitationConfig;

    /**
     * @var \Magento\Invitation\Helper\Data
     */
    protected $_invitationHelper;

    /**
     * @param \Magento\Invitation\Model\Config $invitationConfig
     * @param \Magento\Invitation\Helper\Data $invitationHelper
     */
    public function __construct(
        \Magento\Invitation\Model\Config $invitationConfig,
        \Magento\Invitation\Helper\Data $invitationHelper
    ) {
        $this->_invitationConfig = $invitationConfig;
        $this->_invitationHelper = $invitationHelper;
    }

    /**
     * Check if registration is allowed
     *
     * @param \Magento\Customer\Model\Registration $subject
     * @param boolean $invocationResult
     *
     * @return boolean
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsAllowed(\Magento\Customer\Model\Registration $subject, $invocationResult)
    {
        if (!$this->_invitationConfig->isEnabledOnFront()) {
            return $invocationResult;
        }

        if (!$invocationResult) {
            $this->_invitationHelper->isRegistrationAllowed(false);
        } else {
            $this->_invitationHelper->isRegistrationAllowed(true);
            $invocationResult = !$this->_invitationConfig->getInvitationRequired();
        }
        return $invocationResult;
    }
}
