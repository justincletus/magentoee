<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Model\ResourceModel\Item;

/**
 * Gift registry item option resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Option extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_giftregistry_item_option', 'option_id');
    }
}
