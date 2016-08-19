<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Model\ResourceModel\Reward;

/**
 * Reward collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Reward\Model\Reward', 'Magento\Reward\Model\ResourceModel\Reward');
    }

    /**
     * Add filter by website id
     *
     * @param int|array $websiteId
     * @return $this
     */
    public function addWebsiteFilter($websiteId)
    {
        $this->getSelect()->where(
            is_array($websiteId) ? 'main_table.website_id IN (?)' : 'main_table.website_id = ?',
            $websiteId
        );
        return $this;
    }
}
