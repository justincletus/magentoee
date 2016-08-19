<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Archive order action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($orderId) {
            $this->_archiveModel->archiveOrdersById($orderId);
            $this->messageManager->addSuccess(__('We have archived the order.'));
            $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        } else {
            $this->messageManager->addError(__('Please specify the order ID to be archived.'));
            $resultRedirect->setPath('sales/order');
        }

        return $resultRedirect;
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesArchive::add');
    }
}
