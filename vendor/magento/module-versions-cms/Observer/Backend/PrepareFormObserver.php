<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Observer\Backend;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\VersionsCms\Model\Page\Revision;

/**
 * Prepare edit form of CMS Page
 */
class PrepareFormObserver implements ObserverInterface
{
    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $page;

    /**
     * @var \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected $baseFieldset;

    /**
     * @var \Magento\Framework\Data\Form\Element\Select
     */
    protected $isActiveElement;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $sourceYesno;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\VersionsCms\Model\Page\RevisionFactory
     */
    protected $revisionFactory;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $backendAuthSession;

    /**
     * @param \Magento\VersionsCms\Model\Config $config
     * @param \Magento\Config\Model\Config\Source\Yesno $sourceYesno
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\VersionsCms\Model\Page\RevisionFactory $revisionFactory
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     */
    public function __construct(
        \Magento\VersionsCms\Model\Config $config,
        \Magento\Config\Model\Config\Source\Yesno $sourceYesno,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\VersionsCms\Model\Page\RevisionFactory $revisionFactory,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Backend\Model\Auth\Session $backendAuthSession
    ) {
        $this->config               = $config;
        $this->sourceYesno          = $sourceYesno;
        $this->coreRegistry         = $coreRegistry;
        $this->revisionFactory      = $revisionFactory;
        $this->backendUrl           = $backendUrl;
        $this->authorization        = $authorization;
        $this->backendAuthSession   = $backendAuthSession;
    }

    /**
     * Prepare Form
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $this->page = $this->coreRegistry->registry('cms_page');

        $this->extractFormElements($observer);
        $this->disableActiveElement();

        $revisionAvailable = false;
        if ($this->page) {
            $this->addUnderVersionField();

            $revision = $this->getRevision();
            if ($revision instanceof Revision) {
                $this->addRevisionLink($revision);
                $revisionAvailable = true;
            }

            $this->addStatusField($revisionAvailable);
        }

        $this->disableAllElements($revisionAvailable);

        return $this;
    }

    /**
     * Extract Form elements
     *
     * @param EventObserver $observer
     * @return void
     */
    protected function extractFormElements(EventObserver $observer)
    {
        $form = $observer->getEvent()->getForm();
        $this->baseFieldset = $form->getElement('base_fieldset');
        $this->isActiveElement = $form->getElement('is_active');
    }

    /**
     * Disable active element if user does not have publish permission
     *
     * @return void
     */
    protected function disableActiveElement()
    {
        if ($this->isActiveElement && !$this->config->canCurrentUserPublishRevision()) {
            $this->isActiveElement->setDisabled(true);
        }
    }

    /**
     * Add select "Under Version Control" to Form that contains two options: Yes/No
     *
     * @return void
     */
    protected function addUnderVersionField()
    {
        $this->baseFieldset->addField(
            'under_version_control',
            'select',
            [
                'label' => __('Under Version Control'),
                'title' => __('Under Version Control'),
                'name' => 'under_version_control',
                'values' => $this->sourceYesno->toOptionArray()
            ]
        );
    }

    /**
     * Get revision of page
     *
     * @return Revision|null
     */
    protected function getRevision()
    {
        $revision = null;

        if ($this->page->getPublishedRevisionId() && $this->page->getUnderVersionControl()) {
            $userId = $this->backendAuthSession->getUser()->getId();
            $accessLevel = $this->config->getAllowedAccessLevel();

            /** @var Revision $revision */
            $revision = $this->revisionFactory->create()->loadWithRestrictions(
                $accessLevel,
                $userId,
                $this->page->getPublishedRevisionId()
            );

            $revision = $revision->getId() ? $revision : null;
        }

        return $revision;
    }

    /**
     * Add link to revision to Form
     *
     * @param Revision $revision
     * @return void
     */
    protected function addRevisionLink(Revision $revision)
    {
        $revisionNumber = $revision->getRevisionNumber();
        $versionLabel = $revision->getLabel();

        $this->page->setPublishedRevisionLink(__('%1; rev #%2', $versionLabel, $revisionNumber));

        $this->baseFieldset->addField(
            'published_revision_link',
            'link',
            [
                'label' => __('Currently Published Revision'),
                'href' => $this->backendUrl->getUrl(
                    'adminhtml/cms_page_revision/edit',
                    ['page_id' => $this->page->getId(), 'revision_id' => $this->page->getPublishedRevisionId()]
                ),
                'class' => 'admin__field-value control-value'
            ]
        );
    }

    /**
     * Add field of label type that contains status of revision
     *
     * @param bool $revisionAvailable
     * @return void
     */
    protected function addStatusField($revisionAvailable)
    {
        if (!$revisionAvailable && $this->page->getId() && $this->page->getUnderVersionControl()) {
            $this->baseFieldset->addField('published_revision_status', 'label', ['bold' => true]);
            $this->page->setPublishedRevisionStatus(__('The published revision is unavailable.'));
        }
    }

    /**
     * Disable all elements of Form if user does not have save revision permission
     *
     * @param bool $revisionAvailable
     * @return void
     */
    protected function disableAllElements($revisionAvailable)
    {
        if ($revisionAvailable && !$this->authorization->isAllowed('Magento_VersionsCms::save_revision')) {
            foreach ($this->baseFieldset->getElements() as $element) {
                $element->setDisabled(true);
            }
        }
    }
}
