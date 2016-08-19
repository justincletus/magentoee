<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Controller\Guest;

use Magento\Rma\Model\Rma;

class View extends \Magento\Rma\Controller\Guest
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * View concrete rma
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->_loadValidRma();
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }

        $resultPage = $this->resultPageFactory->create();
        $this->_objectManager->get('Magento\Sales\Helper\Guest')->getBreadcrumbs($resultPage);
        $resultPage->getConfig()->getTitle()->set(
            __('Return #%1', $this->_coreRegistry->registry('current_rma')->getIncrementId())
        );
        return $resultPage;
    }
}
