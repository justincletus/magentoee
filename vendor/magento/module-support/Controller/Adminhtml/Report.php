<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Controller\Adminhtml;

/**
 * Base controller action for all report actions
 */
abstract class Report extends \Magento\Backend\App\Action
{
    /**
     * Get access permission state
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Support::support_report');
    }
}
