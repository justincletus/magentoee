<?php
/**
 * Edit version of CMS page
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version;

use Magento\Backend\App\Action;

class Edit extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Edit
{
    /**
     * @var VersionProvider
     */
    protected $versionProvider;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\VersionsCms\Model\PageLoader $pageLoader
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param VersionProvider $versionProvider
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\VersionsCms\Model\PageLoader $pageLoader,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        VersionProvider $versionProvider
    ) {
        $this->versionProvider = $versionProvider;
        parent::__construct($context, $resultPageFactory, $registry, $pageLoader, $cmsConfig);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page');
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Cms::cms_page');
        $resultPage->addBreadcrumb(__('CMS'), __('CMS'));
        $resultPage->addBreadcrumb(__('Manage Pages'), __('Manage Pages'));
        return $resultPage;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $version = $this->versionProvider->get($this->_request->getParam('version_id'));

        if (!$version->getId()) {
            $this->messageManager->addError(__('We could not load the specified revision.'));
            /** \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath(
                'adminhtml/cms_page/edit',
                ['page_id' => $this->getRequest()->getParam('page_id')]
            );
        }

        $this->pageLoader->load($this->_request->getParam('page_id'));

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $_data = $version->getData();
            $_data = array_merge($_data, $data);
            $version->setData($_data);
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(__('Edit Version'), __('Edit Version'));
        $resultPage->getConfig()->getTitle()->prepend(__('Pages'));
        return $resultPage;
    }
}
