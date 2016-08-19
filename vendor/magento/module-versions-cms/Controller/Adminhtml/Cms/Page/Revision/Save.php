<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision;

use Magento\Backend\App\Action;
use Magento\Cms\Controller\Adminhtml\Page\PostDataProcessor;
use Magento\VersionsCms\Controller\Adminhtml\Cms\Page\RevisionInterface;
use Magento\VersionsCms\Model\Page\RevisionProvider;

class Save extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Save implements RevisionInterface
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authentication;

    /**
     * @var RevisionProvider
     */
    protected $revisionProvider;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     * @param \Magento\Backend\Model\Auth\Session $authentication
     * @param RevisionProvider $revisionProvider
     */
    public function __construct(
        Action\Context $context,
        PostDataProcessor $dataProcessor,
        \Magento\Backend\Model\Auth\Session $authentication,
        RevisionProvider $revisionProvider
    ) {
        $this->authentication = $authentication;
        $this->revisionProvider = $revisionProvider;
        parent::__construct($context, $dataProcessor);
    }

    /**
     * {@inheritdoc}
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_VersionsCms::save_revision');
    }

    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $data = $this->dataProcessor->filter($data);
            // init model and set data
            $revision = $this->revisionProvider->get($this->_request->getParam('revision_id'), $this->_request);
            $revision->setData($data)->setUserId($this->authentication->getUser()->getId());

            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect(
                    'adminhtml/*/' . $this->getRequest()->getParam('back'),
                    ['page_id' => $revision->getPageId(), 'revision_id' => $revision->getId()]
                );
                return;
            }

            // try to save it
            try {
                // save the data
                $revision->save();

                // display success message
                $this->messageManager->addSuccess(__('You have saved the revision.'));
                // clear previously saved data from session
                $this->_session->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        'adminhtml/*/' . $this->getRequest()->getParam('back'),
                        ['page_id' => $revision->getPageId(), 'revision_id' => $revision->getId()]
                    );
                    return;
                }
                // go to grid
                $this->_redirect(
                    'adminhtml/cms_page_version/edit',
                    ['page_id' => $revision->getPageId(), 'version_id' => $revision->getVersionId()]
                );
                return;
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // save data in session
                $this->_session->setFormData($data);
                // redirect to edit form
                $this->_redirect(
                    'adminhtml/*/edit',
                    [
                        'page_id' => $this->getRequest()->getParam('page_id'),
                        'revision_id' => $this->getRequest()->getParam('revision_id')
                    ]
                );
                return;
            }
        }
    }
}
