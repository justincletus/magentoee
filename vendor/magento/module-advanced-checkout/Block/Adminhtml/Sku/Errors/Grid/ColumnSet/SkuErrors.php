<?php
/**
 * Sku Errors Column Set
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\Grid\ColumnSet;

class SkuErrors extends \Magento\Backend\Block\Widget\Grid\ColumnSet
{
    /**
     * Retrieve row css class for specified item
     *
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function getRowClass(\Magento\Framework\DataObject $item)
    {
        if ($item->getCode() == \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED) {
            return 'qty-not-available';
        }
        return '';
    }
}
