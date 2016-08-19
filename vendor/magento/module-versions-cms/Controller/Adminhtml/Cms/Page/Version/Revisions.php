<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version;

use Magento\Backend\App\Action;

class Revisions extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\VersionsCms\Model\PageLoader
     */
    protected $pageLoader;

    /**
     * @var VersionProvider
     */
    protected $versionProvider;

    /**
     * @param Action\Context $context
     * @param \Magento\VersionsCms\Model\PageLoader $pageLoader
     * @param VersionProvider $versionProvider
     */
    public function __construct(
        Action\Context $context,
        \Magento\VersionsCms\Model\PageLoader $pageLoader,
        VersionProvider $versionProvider
    ) {
        $this->pageLoader = $pageLoader;
        $this->versionProvider = $versionProvider;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page');
    }

    /**
     * Action for ajax grid with revisions
     *
     * @return void
     */
    public function execute()
    {
        $this->versionProvider->get($this->_request->getParam('version_id'));
        $this->pageLoader->load($this->_request->getParam('page_id'));

        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Pages'));
        $this->_view->renderLayout();
    }
}
