<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Revision\Edit\Tab;

/**
 * Main tab with cms page attributes and some modifications to CE version
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Content extends \Magento\Cms\Block\Adminhtml\Page\Edit\Tab\Content
{
    /**
     * Cms data
     *
     * @var \Magento\VersionsCms\Helper\Data
     */
    protected $_cmsData;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\VersionsCms\Helper\Data $cmsData
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\VersionsCms\Helper\Data $cmsData,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        array $data = []
    ) {
        $this->_cmsData = $cmsData;
        $this->_backendAuthSession = $backendAuthSession;
        parent::__construct($context, $registry, $formFactory, $wysiwygConfig, $data);
    }

    /**
     * Preparing form by adding extra fields.
     * Adding on change js call.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var \Magento\VersionsCms\Model\ResourceModel\Page\Revision $model */
        $model = $this->_coreRegistry->registry('cms_page');
        parent::_prepareForm();

        $this->_cmsData->addAttributeToFormElements('data-role', 'cms-revision-form-changed', $this->getForm());

        /* @var $fieldset \Magento\Framework\Data\Form\Element\Fieldset */
        $fieldset = $this->getForm()->getElement('content_fieldset');

        if ($model->getPageId()) {
            $fieldset->addField('page_id', 'hidden', ['name' => 'page_id']);

            $fieldset->addField('version_id', 'hidden', ['name' => 'version_id']);

            $fieldset->addField('revision_id', 'hidden', ['name' => 'revision_id']);

            $fieldset->addField('label', 'hidden', ['name' => 'label']);

            $fieldset->addField('user_id', 'hidden', ['name' => 'user_id']);
        }

        $this->getForm()->setValues($model->getData());

        // setting current user id for new version functionality.
        // in posted data there will be current user
        $this->getForm()->getElement('user_id')->setValue($this->_backendAuthSession->getUser()->getId());

        return $this;
    }

    /**
     * Check permission for passed action
     * Rewrite CE save permission to EE save_revision
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        if ($action == 'Magento_Cms::save') {
            $action = 'Magento_VersionsCms::save_revision';
        }
        return parent::_isAllowedAction($action);
    }
}
