<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Reward Helper for operations with customer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Helper;

use Magento\Store\Model\Store;

class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Return Unsubscribe notification URL
     *
     * @param string|bool $notification Notification type
     * @param int|string|Store $storeId
     * @return string
     */
    public function getUnsubscribeUrl($notification = false, $storeId = null)
    {
        $params = [];

        if ($notification) {
            $params['notification'] = $notification;
        }
        if ($storeId !== null) {
            $params['store_id'] = $storeId;
        }
        return $this->_storeManager->getStore($storeId)->getUrl('magento_reward/customer/unsubscribe/', $params);
    }
}
