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

class Publish extends \Magento\Backend\App\Action implements RevisionInterface
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
        return $this->_authorization->isAllowed('Magento_VersionsCms::publish_revision');
    }

    /**
     * Publishing revision
     *
     * @return void
     */
    public function execute()
    {
        $revision = $this->revisionProvider->get((int)$this->_request->getParam('revision_id'), $this->_request);

        try {
            $revision->publish();
            // display success message
            $this->messageManager->addSuccess(__('You have published the revision.'));
            $this->_redirect('adminhtml/cms_page/edit', ['page_id' => $revision->getPageId()]);
            return;
        } catch (\Exception $e) {
            // display error message
            $this->messageManager->addError($e->getMessage());
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
