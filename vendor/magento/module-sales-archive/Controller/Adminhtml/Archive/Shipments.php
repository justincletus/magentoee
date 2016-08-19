<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

use Magento\Framework\Controller\ResultFactory;

class Shipments extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Shipments view page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magento_SalesArchive::sales_archive_shipments');
        $resultPage->getConfig()->getTitle()->prepend(__('Shipments'));

        return $resultPage;
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesArchive::shipments');
    }
}
