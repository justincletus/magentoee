<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version;

use Magento\Backend\App\Action;
use Magento\Cms\Controller\Adminhtml\Page\PostDataProcessor;

class NewAction extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\NewAction
{
    /**
     * @var PostDataProcessor
     */
    protected $filter;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @var VersionProvider
     */
    protected $versionProvider;

    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $resultForwardFactory;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $filter
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param VersionProvider $versionProvider
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        PostDataProcessor $filter,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        VersionProvider $versionProvider
    ) {
        $this->filter = $filter;
        $this->_backendAuthSession = $authSession;
        $this->_cmsConfig = $cmsConfig;
        $this->versionProvider = $versionProvider;
        parent::__construct($context, $resultForwardFactory);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_cmsConfig->canCurrentUserSaveVersion();
    }

    /**
     * New Version
     *
     * @return void
     */
    public function execute()
    {
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            // init model and set data
            $version = $this->versionProvider->get($this->_request->getParam('version_id'));

            $version->addData($data)->unsetData($version->getIdFieldName());

            // only if user not specified we set current user as owner
            if (!$version->getUserId()) {
                $version->setUserId($this->_backendAuthSession->getUser()->getId());
            }

            if (isset($data['revision_id'])) {
                $data = $this->filter->filter($data);
                $version->setInitialRevisionData($data);
            }

            // try to save it
            try {
                $version->save();
                // display success message
                $this->messageManager->addSuccess(__('You have created the new version.'));
                // clear previously saved data from session
                $this->_session->setFormData(false);
                if (isset($data['revision_id'])) {
                    $this->_redirect(
                        'adminhtml/cms_page_revision/edit',
                        [
                            'page_id' => $version->getPageId(),
                            'revision_id' => $version->getLastRevision()->getId()
                        ]
                    );
                } else {
                    $this->_redirect(
                        'adminhtml/cms_page_version/edit',
                        ['page_id' => $version->getPageId(), 'version_id' => $version->getId()]
                    );
                }
                return;
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                if ($this->_redirect->getRefererUrl()) {
                    // save data in session
                    $this->_session->setFormData($data);
                }
                // redirect to edit form
                $editUrl = $this->getUrl(
                    'adminhtml/cms_page/edit',
                    ['page_id' => $this->getRequest()->getParam('page_id')]
                );
                $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($editUrl));
                return;
            }
        }
    }
}
