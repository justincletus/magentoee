<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision;

use Magento\Backend\App\Action;
use Magento\VersionsCms\Controller\Adminhtml\Cms\Page\RevisionInterface;
use Magento\VersionsCms\Model\Page\RevisionProvider;

class Edit extends \Magento\Cms\Controller\Adminhtml\Page\Edit implements RevisionInterface
{
    /**
     * @var RevisionProvider
     */
    protected $revisionProvider;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param RevisionProvider $revisionProvider
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        RevisionProvider $revisionProvider
    ) {
        $this->revisionProvider = $revisionProvider;
        parent::__construct($context, $resultPageFactory, $registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page');
    }

    /**
     * Edit revision of CMS page
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $revisionId = $this->getRequest()->getParam('revision_id');
        $revision = $this->revisionProvider->get($revisionId, $this->_request);

        if ($revisionId && !$revision->getId()) {
            $this->messageManager->addError(__('We could not load the specified revision.'));
            /** \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath(
                'adminhtml/cms_page/edit',
                ['page_id' => $this->getRequest()->getParam('page_id')]
            );
        }

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $_data = $revision->getData();
            $_data = array_merge($_data, $data);
            $revision->setData($_data);
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(__('Edit Revision'), __('Edit Revision'));
        return $resultPage;
    }
}
