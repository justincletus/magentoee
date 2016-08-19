<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\VersionsCms\Controller\Page\Revision;


use Magento\VersionsCms\Model\Page\RevisionProvider;
use Magento\Framework\Controller;

/**
 * Class Drop
 */
class Drop extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $cmsPage;

    /**Drop.php
     * @var RevisionProvider
     */
    protected $revisionProvider;

    /**
     * @var \Magento\Framework\App\DesignInterface
     */
    protected $design;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Cms\Model\Page $page
     * @param RevisionProvider $revisionProvider
     * @param \Magento\Framework\App\DesignInterface $design
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Cms\Model\Page $page,
        RevisionProvider $revisionProvider,
        \Magento\Framework\App\DesignInterface $design,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver
    ) {
        $this->cmsPage = $page;
        $this->revisionProvider = $revisionProvider;
        $this->design = $design;
        $this->storeManager = $storeManager;
        $this->localeResolver = $localeResolver;
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
     * Generates preview of page. Assumed to be run in frontend area
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function previewFrontendPage()
    {
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        if (!empty($data) && isset($data['page_id'])) {
            // init model and set data
            $page = $this->cmsPage->load($data['page_id']);
            if (!$page->getId()) {
                /** @var Controller\Result\Forward $resultForward */
                $resultForward = $this->resultFactory->create(Controller\ResultFactory::TYPE_FORWARD);
                return $resultForward->forward('noroute');
            }

            /**
             * If revision was selected load it and get data for preview from it
             */
            $tempData = null;
            if (isset($data['preview_selected_revision']) && $data['preview_selected_revision']) {
                $revision = $this->revisionProvider->get($data['preview_selected_revision'], $this->_request);
                if ($revision->getId()) {
                    $tempData = $revision->getData();
                }
            }

            /**
             * If there was no selected revision then use posted data
             */
            if (is_null($tempData)) {
                $tempData = $data;
            }

            /**
             * Posting posted data in page model
             */
            $page->addData($tempData);

            /**
             * Retrieve store id from page model or if it was passed from post
             */
            $selectedStoreId = $page->getStoreId();
            if (is_array($selectedStoreId)) {
                $selectedStoreId = array_shift($selectedStoreId);
            }

            if (isset($data['preview_selected_store']) && $data['preview_selected_store']) {
                $selectedStoreId = $data['preview_selected_store'];
            } else {
                if (!$selectedStoreId) {
                    $defaultStore = $this->storeManager->getDefaultStoreView();
                    if (!$defaultStore) {
                        $allStores = $this->storeManager->getStores();
                        if (isset($allStores[0])) {
                            $defaultStore = $allStores[0];
                        }
                    }
                    $selectedStoreId = $defaultStore ? $defaultStore->getId() : null;
                }
            }
            $selectedStoreId = (int)$selectedStoreId;

            /**
             * Emulating front environment
             */
            $this->localeResolver->emulate($selectedStoreId);
            $this->storeManager->setCurrentStore($this->storeManager->getStore($selectedStoreId));

            $theme = $this->_objectManager->get(
                'Magento\Framework\View\DesignInterface'
            )->getConfigurationDesignTheme(
                null,
                ['store' => $selectedStoreId]
            );
            $this->_objectManager->get('Magento\Framework\View\DesignInterface')->setDesignTheme($theme, 'frontend');

            $designChange = $this->design->loadChange($selectedStoreId);

            if ($designChange->getData()) {
                $this->_objectManager->get('Magento\Framework\View\DesignInterface')->setDesignTheme($designChange->getDesign());
            }

            /** @var \Magento\Cms\Helper\Page $helper */
            $helper = $this->_objectManager->get('Magento\Cms\Helper\Page');

            $resultPage = $helper->prepareResultPage($this);
            if ($resultPage) {
                // add handles used to render cms page on frontend
                $resultPage->addHandle('default');
                $resultPage->addHandle('cms_page_view');

                $this->localeResolver->revert();
                return $resultPage;
            }
        }

        /** @var Controller\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(Controller\ResultFactory::TYPE_FORWARD);
        return $resultForward->forward('noroute');
    }

    /**
     * Generates preview of page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_objectManager->get('Magento\Framework\Translate\Inline\StateInterface')->suspend();

        /** @var \Magento\Framework\App\State $state */
        $state = $this->_objectManager->get('Magento\Framework\App\State');
        return $state->emulateAreaCode('frontend', [$this, 'previewFrontendPage']);
    }
}
