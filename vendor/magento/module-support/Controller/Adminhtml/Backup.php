<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Controller\Adminhtml;

/**
 * General abstract class for backup actions
 */
abstract class Backup extends \Magento\Backend\App\Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function getResultPage()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $pageResult */
        $pageResult = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $pageResult->setActiveMenu('Magento_Support::support_backup');
        $pageResult->addBreadcrumb(__('Support'), __('Support'));
        $pageResult->addBreadcrumb(__('Data Collector'), __('Data Collector'));

        return $pageResult;
    }

    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Support::support_backup');
    }
}
