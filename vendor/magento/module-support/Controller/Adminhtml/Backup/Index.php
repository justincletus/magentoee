<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Controller\Adminhtml\Backup;

/**
 * Index backup action
 */
class Index extends \Magento\Support\Controller\Adminhtml\Backup
{
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $pageResult */
        $pageResult = $this->getResultPage();
        $pageResult->getConfig()->getTitle()->prepend(__('Data Collector'));
        return $pageResult;
    }
}
