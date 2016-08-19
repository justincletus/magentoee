<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Model\ResourceModel\Reward\Rate;

/**
 * Reward rate collection
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
        $this->_init('Magento\Reward\Model\Reward\Rate', 'Magento\Reward\Model\ResourceModel\Reward\Rate');
    }

    /**
     * Add filter by website id
     *
     * @param int|array $websiteId
     * @return $this
     */
    public function addWebsiteFilter($websiteId)
    {
        $websiteId = array_merge((array)$websiteId, [0]);
        $this->getSelect()->where('main_table.website_id IN (?)', $websiteId);
        return $this;
    }
}
