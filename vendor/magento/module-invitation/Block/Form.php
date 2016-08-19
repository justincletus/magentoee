<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Front end helper block to render form
 *
 */
namespace Magento\Invitation\Block;

class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * Invitation Config
     *
     * @var \Magento\Invitation\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Invitation\Model\Config $config
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Invitation\Model\Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_config = $config;
    }

    /**
     * Returns maximal number of invitations to send in one try
     *
     * @return int
     */
    public function getMaxInvitationsPerSend()
    {
        return $this->_config->getMaxInvitationsPerSend();
    }

    /**
     * Returns whether custom invitation message allowed
     *
     * @return bool
     */
    public function isInvitationMessageAllowed()
    {
        return $this->_config->isInvitationMessageAllowed();
    }
}
