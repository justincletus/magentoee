<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Model\ResourceModel\GiftRegistry;

/**
 * Gift registry data grid collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\GiftRegistry\Model\ResourceModel\Type\Collection
{
    /**
     * Add sore data for load
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addStoreData();
        return $this;
    }
}
