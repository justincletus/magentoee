<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Gift wrapping adminhtml block for create order items
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Sales\Order\Create;

class Items extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * Get order item from parent block
     *
     * @return \Magento\Sales\Model\Order\Item
     * @codeCoverageIgnore
     */
    public function getItem()
    {
        return $this->getParentBlock()->getData('item');
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $_item = $this->getItem();
        if ($_item && $_item->getGwId()) {
            return parent::_toHtml();
        } else {
            return false;
        }
    }
}
