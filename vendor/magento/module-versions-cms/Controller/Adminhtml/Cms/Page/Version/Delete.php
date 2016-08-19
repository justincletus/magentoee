<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version;

use Magento\Backend\App\Action;

class Delete extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Delete
{
    /**
     * @var VersionProvider
     */
    protected $versionProvider;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @param Action\Context $context
     * @param VersionProvider $versionProvider
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     */
    public function __construct(
        Action\Context $context,
        VersionProvider $versionProvider,
        \Magento\VersionsCms\Model\Config $cmsConfig
    ) {
        $this->versionProvider = $versionProvider;
        $this->_cmsConfig = $cmsConfig;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_cmsConfig->canCurrentUserDeleteVersion();
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('version_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            // init model
            $version = $this->versionProvider->get($this->_request->getParam('version_id'));
            $error = false;
            try {
                $version->delete();
                // display success message
                $this->messageManager->addSuccess(__('You have deleted the version.'));
                return $resultRedirect->setPath('adminhtml/cms_page/edit', ['page_id' => $version->getPageId()]);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->messageManager->addError(__('Something went wrong while deleting this version.'));
                $error = true;
            }

            // go back to edit form
            if ($error) {
                return $resultRedirect->setPath('adminhtml/*/edit', ['_current' => true]);
            }
        }
        // display error message
        $this->messageManager->addError(__("We can't find a version to delete."));
        // go to grid
        return $resultRedirect->setPath('adminhtml/cms_page/edit', ['_current' => true]);
    }
}
