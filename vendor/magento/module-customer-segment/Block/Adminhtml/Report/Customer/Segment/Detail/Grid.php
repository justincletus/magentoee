<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer Segments Detail grid
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerSegment\Block\Adminhtml\Report\Customer\Segment\Detail;

class Grid extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * Instantiate collection and set required data joins
     *
     * @return \Magento\CustomerSegment\Block\Adminhtml\Report\Customer\Segment\Detail\Grid
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->load();
        return $this->getCollection();
    }
}
