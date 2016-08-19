<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page;

use Magento\Backend\App\Action\Context;
use Magento\VersionsCms\Model\PageLoader;

class Versions extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\VersionsCms\Model\PageLoader
     */
    protected $pageLoader;

    /**
     * @param Context $context
     * @param PageLoader $pageLoader
     */
    public function __construct(
        Context $context,
        PageLoader $pageLoader
    ) {
        parent::__construct($context);
        $this->pageLoader = $pageLoader;
    }

    /**
     * {@inheritdoc}
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page');
    }

    /**
     * Action for versions ajax tab
     *
     * @return void
     */
    public function execute()
    {
        $this->pageLoader->load($this->_request->getParam('page_id'));

        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Pages'));
        $this->_view->renderLayout();
    }
}
