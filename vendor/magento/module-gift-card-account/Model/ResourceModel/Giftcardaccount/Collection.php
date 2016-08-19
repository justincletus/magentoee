<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardAccount\Model\ResourceModel\Giftcardaccount;

/**
 * GiftCardAccount Resource Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\GiftCardAccount\Model\Giftcardaccount',
            'Magento\GiftCardAccount\Model\ResourceModel\Giftcardaccount'
        );
    }

    /**
     * Filter collection by specified websites
     *
     * @param array|int $websiteIds
     * @return $this
     */
    public function addWebsiteFilter($websiteIds)
    {
        $this->getSelect()->where('main_table.website_id IN (?)', $websiteIds);
        return $this;
    }
}
