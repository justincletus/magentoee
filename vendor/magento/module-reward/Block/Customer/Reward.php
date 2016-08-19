<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer My Account -> Reward Points container
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @codeCoverageIgnore
 */
namespace Magento\Reward\Block\Customer;

class Reward extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * Set template variables
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setBackUrl($this->getUrl('customer/account/'));
        return parent::_toHtml();
    }
}
