<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result;

class Edit extends \Magento\Cms\Controller\Adminhtml\Page\Edit
{
    /**
     * @var \Magento\VersionsCms\Model\PageLoader
     */
    protected $pageLoader;

    /**
     * @var array
     */
    protected $_handles = [];

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

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
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\VersionsCms\Model\PageLoader $pageLoader,
        \Magento\VersionsCms\Model\Config $cmsConfig
    ) {
        $this->pageLoader = $pageLoader;
        $this->_cmsConfig = $cmsConfig;
        parent::__construct($context, $resultPageFactory, $registry);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addHandle($this->_handles);

        //load layout, set active menu and breadcrumbs
        $resultPage->setActiveMenu('Magento_VersionsCms::versionscms_page_page');
        $resultPage->addBreadcrumb(__('CMS'), __('CMS'));
        $resultPage->addBreadcrumb(__('Manage Pages'), __('Manage Pages'));
        return $resultPage;
    }

    /**
     * Edit CMS page
     *
     * @return \Magento\Backend\Model\View\Result\Page $resultPage
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $page = $this->pageLoader->load($this->_request->getParam('page_id'));

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $page->setData($data);
        }

        if ($page->getId()) {
            if ($page->getUnderVersionControl()) {
                $this->_handles[] = 'adminhtml_cms_page_edit_changes';
            }
        } elseif (!$page->hasUnderVersionControl()) {
            $page->setUnderVersionControl((int)$this->_cmsConfig->getDefaultVersioningStatus());
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $page->getId() ? __('Edit Page') : __('New Page'),
            $page->getId() ? __('Edit Page') : __('New Page')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Pages'));
        $resultPage->getConfig()->getTitle()->prepend($page->getId() ? $page->getTitle() : __('New Page'));
        return $resultPage;
    }
}
