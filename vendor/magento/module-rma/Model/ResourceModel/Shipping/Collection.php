<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Model\ResourceModel\Shipping;

use Magento\Rma\Api\Data\TrackSearchResultInterface;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;

/**
 * RMA shipping collection
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Collection extends AbstractCollection implements TrackSearchResultInterface
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Rma\Model\Shipping', 'Magento\Rma\Model\ResourceModel\Shipping');
    }
}
