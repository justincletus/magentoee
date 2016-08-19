<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Model\Sales\Quote;

/**
 * Customer Quote Address model
 *
 * @method \Magento\CustomerCustomAttributes\Model\ResourceModel\Sales\Quote\Address _getResource()
 * @method \Magento\CustomerCustomAttributes\Model\ResourceModel\Sales\Quote\Address getResource()
 * @method \Magento\CustomerCustomAttributes\Model\Sales\Quote\Address setEntityId(int $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Address extends \Magento\CustomerCustomAttributes\Model\Sales\Address\AbstractAddress
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\CustomerCustomAttributes\Model\ResourceModel\Sales\Quote\Address');
    }
}
