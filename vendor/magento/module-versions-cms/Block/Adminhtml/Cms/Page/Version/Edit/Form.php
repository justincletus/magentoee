<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Version\Edit;

/**
 * Form for version edit page
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var string
     */
    protected $_template = 'page/version/form.phtml';

    /**
     * Cms data
     *
     * @var \Magento\VersionsCms\Helper\Data
     */
    protected $_cmsData;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\VersionsCms\Helper\Data $cmsData
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\VersionsCms\Helper\Data $cmsData,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        array $data = []
    ) {
        $this->_cmsData = $cmsData;
        $this->_cmsConfig = $cmsConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Preparing from for version page
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('adminhtml/*/save', ['_current' => true]),
                    'method' => 'post',
                ],
            ]
        );

        $form->setUseContainer(true);

        /* @var $model \Magento\Cms\Model\Page */
        $version = $this->_coreRegistry->registry('cms_page_version');

        $isOwner = $this->_cmsConfig->isCurrentUserOwner($version->getUserId());
        $isPublisher = $this->_cmsConfig->canCurrentUserPublishRevision();

        $fieldset = $form->addFieldset(
            'version_fieldset',
            ['legend' => __('Version Information'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField('version_id', 'hidden', ['name' => 'version_id']);

        $fieldset->addField('page_id', 'hidden', ['name' => 'page_id']);

        $fieldset->addField(
            'label',
            'text',
            ['name' => 'label', 'label' => __('Version Label'), 'disabled' => !$isOwner, 'required' => true]
        );

        $fieldset->addField(
            'access_level',
            'select',
            [
                'label' => __('Access Level'),
                'title' => __('Access Level'),
                'name' => 'access_level',
                'options' => $this->_cmsData->getVersionAccessLevels(),
                'disabled' => !$isOwner && !$isPublisher
            ]
        );

        if ($isPublisher) {
            $fieldset->addField(
                'user_id',
                'select',
                [
                    'label' => __('Owner'),
                    'title' => __('Owner'),
                    'name' => 'user_id',
                    'options' => $this->_cmsData->getUsersArray(!$version->getUserId()),
                    'required' => !$version->getUserId()
                ]
            );
        }

        $form->setValues($version->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
