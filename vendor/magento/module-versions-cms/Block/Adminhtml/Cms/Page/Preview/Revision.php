<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Preview;

/**
 * Revision selector
 */
class Revision extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\VersionsCms\Model\ResourceModel\Page\Revision\CollectionFactory
     */
    protected $_revisionCollectionFactory;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\VersionsCms\Model\ResourceModel\Page\Revision\CollectionFactory $revisionCollectionFactory
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\VersionsCms\Model\ResourceModel\Page\Revision\CollectionFactory $revisionCollectionFactory,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        array $data = []
    ) {
        $this->_revisionCollectionFactory = $revisionCollectionFactory;
        $this->_cmsConfig = $cmsConfig;
        $this->_backendAuthSession = $backendAuthSession;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve id of currently selected revision
     *
     * @return int
     */
    public function getRevisionId()
    {
        if (!$this->hasRevisionId()) {
            $this->setData('revision_id', (int)$this->getRequest()->getPost('preview_selected_revision'));
        }
        return $this->getData('revision_id');
    }

    /**
     * Prepare array with revisions sorted by versions
     *
     * @return array
     */
    public function getRevisions()
    {
        /* var $collection \Magento\VersionsCms\Model\ResourceModel\Page\Revision\Collection */
        $collection = $this->_revisionCollectionFactory->create()->addPageFilter(
            $this->getRequest()->getParam('page_id')
        )->joinVersions()->addNumberSort()->addVisibilityFilter(
            $this->_backendAuthSession->getUser()->getId(),
            $this->_cmsConfig->getAllowedAccessLevel()
        );

        $revisions = [];

        foreach ($collection->getItems() as $item) {
            if (isset($revisions[$item->getVersionId()])) {
                $revisions[$item->getVersionId()]['revisions'][] = $item;
            } else {
                $revisions[$item->getVersionId()] = [
                    'revisions' => [$item],
                    'label' => $item->getLabel() ? $item->getLabel() : __('N/A'),
                ];
            }
        }
        krsort($revisions);
        reset($revisions);
        return $revisions;
    }
}
