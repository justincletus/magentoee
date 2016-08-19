<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Permission collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogPermissions\Model\ResourceModel\Permission;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\CatalogPermissions\Model\Permission',
            'Magento\CatalogPermissions\Model\ResourceModel\Permission'
        );
    }
}
