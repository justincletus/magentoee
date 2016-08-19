<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Logging\Model\ResourceModel\Event;

/**
 * Logging event changes model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Changes extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_logging_event_changes', 'id');
    }
}
