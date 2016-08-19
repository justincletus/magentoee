<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Revision;

/**
 * Edit revision page
 */
class Edit extends \Magento\Cms\Block\Adminhtml\Page\Edit
{
    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        array $data = []
    ) {
        $this->_cmsConfig = $cmsConfig;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Constructor. Modifying default CE buttons.
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _construct()
    {
        parent::_construct();

        $this->removeButton('delete');

        $this->_objectId = 'revision_id';

        $this->_controller = 'adminhtml_cms_page_revision';
        $this->_blockGroup = 'Magento_VersionsCms';

        $this->setFormActionUrl($this->getUrl('adminhtml/cms_page_revision/save'));

        $objId = $this->getRequest()->getParam($this->_objectId);

        if (!empty($objId) && $this->_cmsConfig->canCurrentUserDeleteRevision()) {
            $this->buttonList->add(
                'delete_revision',
                [
                    'label' => __('Delete'),
                    'class' => 'delete',
                    'onclick' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to delete this revision?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\')'
                ]
            );
        }

        $this->buttonList->add(
            'preview',
            [
                'label' => __('Preview'),
                'class' => 'preview',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'preview',
                            'target' => '#edit_form',
                            'eventData' => ['action' => $this->getPreviewUrl()]
                        ]
                    ]
                ]
            ]
        );

        if ($this->_cmsConfig->canCurrentUserPublishRevision()) {
            $this->buttonList->add(
                'publish',
                [
                    'id' => 'publish_button',
                    'label' => __('Publish'),
                    'onclick' => "setLocation('" . $this->getPublishUrl() . "')",
                    'class' => 'publish' . ($this->_coreRegistry->registry('cms_page')->getId() ? '' : ' no-display')
                ],
                1
            );

            if ($this->_cmsConfig->canCurrentUserSaveRevision()) {
                $this->buttonList->add(
                    'save_publish',
                    [
                        'id' => 'save_publish_button',
                        'label' => __('Save and publish.'),
                        'class' => 'publish no-display',
                        'data_attribute' => [
                            'mage-init' => [
                                'button' => [
                                    'event' => 'save',
                                    'target' => '#edit_form',
                                    'eventData' => ['action' => $this->getSaveAndPublishUrl()]
                                ]
                            ]
                        ]
                    ],
                    1
                );
            }

            $this->buttonList->update('saveandcontinue', 'level', 2);
        }

        if ($this->_cmsConfig->canCurrentUserSaveRevision()) {
            $this->buttonList->update('save', 'label', __('Save'));
            $this->buttonList->update(
                'save',
                'data_attribute',
                ['mage-init' => ['button' => ['event' => 'save', 'target' => '#edit_form']]]
            );
            $this->buttonList->update(
                'saveandcontinue',
                'data_attribute',
                ['mage-init' => ['button' => ['event' => 'preview', 'target' => '#edit_form']]]
            );

            // Adding button to create new version
            $this->buttonList->add(
                'new_version',
                [
                    'id' => 'new_version',
                    'label' => __('Save in a new version.'),
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => [
                                'event' => 'save',
                                'target' => '#edit_form',
                                'eventData' => [
                                    'action' => $this->getNewVersionUrl(),
                                ]
                            ]
                        ]
                    ],
                    'class' => 'new'
                ]
            );

            $this->_formScripts[] = "
                require(['jquery', 'uiPrompt', 'uiAlert', 'prototype'],".
                "function(jQuery, prompt, alert) {
                function newVersionAction(e){
                    e.stopImmediatePropagation();
                    prompt({
                        content: '" . __('You must specify a new version name.') . "',
                        actions: {
                            confirm: function(versionName) {
                                if (versionName == '' || versionName == null) {
                                    alert({
                                        content: '" .
                                            __(
                                                'Please specify a valid name.'
                                            ) .
                                            "'
                                    });

                                    return false;
                                }
                                $('page_label').value = versionName;
                                jQuery('#edit_form').trigger(
                                    'save',
                                    {
                                        action: '" . $this->getNewVersionUrl() . "',
                                    }
                                )
                            }
                        }
                    });
                }

                jQuery('#new_version').on('click', newVersionAction);

                });
            ";
        } else {
            $this->removeButton('save');
            $this->removeButton('saveandcontinue');
        }
        $pageMainTitle = $this->getLayout()->getBlock('page.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($this->getHeaderText());
        }
        return $this;
    }

    /**
     * Retrieve text for header element depending
     * on loaded page and revision
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $revisionNumber = $this->_coreRegistry->registry('cms_page')->getRevisionNumber();
        $title = $this->escapeHtml($this->_coreRegistry->registry('cms_page')->getTitle());

        if ($revisionNumber) {
            return __("Edit Page '%1' Revision #%2", $title, $this->escapeHtml($revisionNumber));
        } else {
            return __("Edit Page '%1' New Revision", $title);
        }
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

    /**
     * Get URL for back button
     *
     * @return string
     */
    public function getBackUrl()
    {
        $revision = $this->_coreRegistry->registry('cms_page');

        return $this->getUrl(
            'adminhtml/cms_page_version/edit',
            [
                'page_id' => $revision ? $revision->getPageId() : null,
                'version_id' => $revision ? $revision->getVersionId() : null
            ]
        );
    }

    /**
     * Get URL for delete button
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('adminhtml/*/delete', ['_current' => true]);
    }

    /**
     * Get URL for publish button
     *
     * @return string
     */
    public function getPublishUrl()
    {
        return $this->getUrl('adminhtml/*/publish', ['_current' => true]);
    }

    /**
     * Get Url for save_publish button
     *
     * @return string
     */
    public function getSaveAndPublishUrl()
    {
        return $this->getUrl('adminhtml/cms_page_revision/save', ['back' => 'publish']);
    }

    /**
     * Get URL for preview button
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->getUrl('adminhtml/*/preview');
    }

    /**
     * Adding info block html before form html
     *
     * @return string
     */
    public function getFormHtml()
    {
        return $this->getChildHtml('revision_info') . parent::getFormHtml();
    }

    /**
     * Save into new version link
     *
     * @return string
     */
    public function getNewVersionUrl()
    {
        return $this->getUrl('adminhtml/cms_page_version/new');
    }
}
