<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Model\ResourceModel\Person;

/**
 * Gift registry entity registrants collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\GiftRegistry\Model\Person', 'Magento\GiftRegistry\Model\ResourceModel\Person');
    }

    /**
     * Apply entity filter to collection
     *
     * @param int $entityId
     * @return $this
     */
    public function addRegistryFilter($entityId)
    {
        $this->getSelect()->where('main_table.entity_id = ?', (int)$entityId);
        return $this;
    }
}
