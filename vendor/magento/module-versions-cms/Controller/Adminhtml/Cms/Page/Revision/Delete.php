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

class Delete extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Delete implements RevisionInterface
{
    /**
     * @var RevisionProvider
     */
    protected $revisionProvider;

    /**
     * @param Action\Context $context
     * @param RevisionProvider $revisionProvider
     */
    public function __construct(Action\Context $context, RevisionProvider $revisionProvider)
    {
        $this->revisionProvider = $revisionProvider;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_VersionsCms::delete_revision');
    }

    /**
     * Delete action
     *
     * @return void
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('revision_id');
        if ($id) {
            try {
                // init model and delete
                $revision = $this->revisionProvider->get((int)$id, $this->_request);
                $revision->delete();
                // display success message
                $this->messageManager->addSuccess(__('You have deleted the revision.'));
                $this->_redirect(
                    'adminhtml/cms_page_version/edit',
                    ['page_id' => $revision->getPageId(), 'version_id' => $revision->getVersionId()]
                );
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->messageManager->addError(__('Something went wrong while deleting the revision.'));
                $error = true;
            }

            // go back to edit form
            if ($error) {
                $this->_redirect('adminhtml/*/edit', ['_current' => true]);
                return;
            }
        }
        // display error message
        $this->messageManager->addError(__("We can't find a revision to delete."));
        // go to grid
        $this->_redirect('adminhtml/cms_page/edit', ['_current' => true]);
    }
}
