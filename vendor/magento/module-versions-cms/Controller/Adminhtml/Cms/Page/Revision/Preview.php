<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision;

use Magento\Backend\App\Action;
use Magento\VersionsCms\Controller\Adminhtml\Cms\Page\RevisionInterface;
use Magento\Framework\Controller;

class Preview extends \Magento\Backend\App\Action implements RevisionInterface
{
    /**
     * @var \Magento\VersionsCms\Model\PageLoader
     */
    protected $pageLoader;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Framework\Session\Config\ConfigInterface
     */
    protected $sessionConfig;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\VersionsCms\Model\PageLoader $pageLoader
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Backend\App\ConfigInterface $config
     * @param \Magento\Framework\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\VersionsCms\Model\PageLoader $pageLoader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\App\ConfigInterface $config,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->pageLoader = $pageLoader;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->sessionConfig = $sessionConfig;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page');
    }

    /**
     * Activate admin session on frontend
     * @return void
     */
    protected function activateAdminSessionOnFrontend()
    {
        $stores = $this->storeManager->getStores();

        /** @var \Magento\Store\Model\Store $store */
        foreach ($stores as $store) {
            $uri = \Zend\Uri\UriFactory::factory($store->getBaseUrl());
            $this->setCookie(
                [
                    'domain' => $uri->getHost(),
                    'path' => $uri->getPath() . \Magento\VersionsCms\Model\Page\Revision::PREVIEW_URI
                ]
            );
        }
    }

    /**
     * Set admin cookie
     *
     * @param array $options
     * @return void
     */
    private function setCookie($options)
    {
        $sessionName = $this->_session->getName();
        $cookieValue = $this->cookieManager->getCookie($sessionName);
        if ($cookieValue) {
            $defaultOptions = [
                'lifetime' => $this->config->getValue(\Magento\Backend\Model\Auth\Session::XML_PATH_SESSION_LIFETIME),
                'path' => $this->sessionConfig->getCookiePath(),
                'domain' => $this->sessionConfig->getCookieDomain(),
                'secure' => $this->sessionConfig->getCookieSecure(),
               'http_only' => $this->sessionConfig->getCookieHttpOnly()
            ];

            $options = array_merge($defaultOptions, $options);
            $this->_session->setUpdatedAt(time());

            $cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                ->setDuration($options['lifetime'])
                ->setPath($options['path'])
                ->setDomain($options['domain'])
                ->setSecure($options['secure'])
                ->setHttpOnly($options['http_only']);
            $this->cookieManager->setPublicCookie($sessionName, $cookieValue, $cookieMetadata);
        }
    }

    /**
     * Prepares page with iframe
     *
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        if (empty($data) || !isset($data['page_id'])) {
            /** @var \Magento\Backend\Model\View\Result\Forward $result */
            $result = $this->resultFactory->create(Controller\ResultFactory::TYPE_FORWARD);
            $result->forward('noroute');
            return $result;
        }

        $this->activateAdminSessionOnFrontend();

        $page = $this->pageLoader->load($this->_request->getParam('page_id'));

        $stores = $page->getStoreId();
        if (isset($data['stores'])) {
            $stores = $data['stores'];
        }

        /*
         * Checking if all stores passed then we should not assign array to block
         */
        $allStores = false;
        if (is_array($stores) && count($stores) == 1 && !array_shift($stores)) {
            $allStores = true;
        }

        /** @var \Magento\Backend\Model\View\Result\Page $result */
        $resultPage = $this->resultFactory->create(Controller\ResultFactory::TYPE_PAGE);
        if (!$allStores) {
            $resultPage->getLayout()->getBlock('store_switcher')->setStoreIds($stores);
        }

        // Setting default values for selected store and revision
        $data['preview_selected_store'] = 0;
        $data['preview_selected_revision'] = 0;

        $resultPage->getLayout()->getBlock('preview_form')->setFormData($data);

        // Remove revision switcher if page is out of version control
        if (!$page->getUnderVersionControl()) {
            $resultPage->getLayout()->unsetChild('tools', 'revision_switcher');
        }
        $resultPage->getConfig()->getTitle()->prepend(__('Pages'));
        return $resultPage;
    }
}
