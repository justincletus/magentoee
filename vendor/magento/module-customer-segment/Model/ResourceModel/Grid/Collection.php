<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

/**
 * Customer segment data grid collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerSegment\Model\ResourceModel\Grid;

class Collection extends \Magento\CustomerSegment\Model\ResourceModel\Segment\Collection
{
    /**
     * Add websites for load
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|\Magento\CustomerSegment\Model\ResourceModel\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();
        return $this;
    }
}
