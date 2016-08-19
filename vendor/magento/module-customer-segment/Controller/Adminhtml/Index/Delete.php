<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

class Delete extends \Magento\CustomerSegment\Controller\Adminhtml\Index
{
    /**
     * Delete customer segment
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            $model = $this->_initSegment('id', true);
            $model->delete();
            $this->messageManager->addSuccess(__('You deleted the segment.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('customersegment/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        } catch (\Exception $e) {
            $this->messageManager->addError(__("We're unable to delete the segement."));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        return $resultRedirect->setPath('customersegment/*/');
    }
}
