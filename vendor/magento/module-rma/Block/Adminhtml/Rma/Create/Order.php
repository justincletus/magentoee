<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Admin RMA create order block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Create;

class Order extends \Magento\Rma\Block\Adminhtml\Rma\Create\AbstractCreate
{
    /**
     * Get Header Text for Order Selection
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Please Select Order');
    }
}
