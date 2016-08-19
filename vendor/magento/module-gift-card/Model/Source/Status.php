<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCard\Model\Source;

class Status extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => \Magento\Sales\Model\Order\Item::STATUS_PENDING, 'label' => __('Ordered')],
            ['value' => \Magento\Sales\Model\Order\Item::STATUS_INVOICED, 'label' => __('Invoiced')]
        ];
    }
}
