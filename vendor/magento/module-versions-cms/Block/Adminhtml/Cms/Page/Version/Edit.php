<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Version;

/**
 * Edit version page
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var string
     */
    protected $_objectId = 'version_id';

    /**
     * @var string
     */
    protected $_blockGroup = 'Magento_VersionsCms';

    /**
     * @var string
     */
    protected $_controller = 'adminhtml_cms_page_version';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

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
        $this->_coreRegistry = $registry;
        $this->_cmsConfig = $cmsConfig;
        parent::__construct($context, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $version = $this->_coreRegistry->registry('cms_page_version');

        // Add 'new button' depending on permission
        if ($this->_cmsConfig->canCurrentUserSaveVersion()) {
            $this->buttonList->add(
                'new',
                [
                    'label' => __('Save as new version.'),
                    'class' => 'new',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => [
                                'event' => 'save',
                                'target' => '#edit_form',
                                'eventData' => ['action' => $this->getNewUrl()],
                            ],
                        ],
                    ]
                ]
            );

            $this->buttonList->add(
                'new_revision',
                [
                    'label' => __('New Revision...'),
                    'onclick' => "setLocation('" . $this->getNewRevisionUrl() . "');",
                    'class' => 'new'
                ]
            );
        }

        $isOwner = $version ? $this->_cmsConfig->isCurrentUserOwner($version->getUserId()) : false;
        $isPublisher = $this->_cmsConfig->canCurrentUserPublishRevision();

        // Only owner can remove version if he has such permissions
        if (!$isOwner || !$this->_cmsConfig->canCurrentUserDeleteVersion()) {
            $this->removeButton('delete');
        }

        // Only owner and publisher can save version
        if (($isOwner || $isPublisher) && $this->_cmsConfig->canCurrentUserSaveVersion()) {
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and continue edit.'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                1
            );
        } else {
            $this->removeButton('save');
        }
    }

    /**
     * Retrieve text for header element depending
     * on loaded page and version
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $versionLabel = $this->escapeHtml($this->_coreRegistry->registry('cms_page_version')->getLabel());
        $title = $this->escapeHtml($this->_coreRegistry->registry('cms_page')->getTitle());

        if (!$versionLabel) {
            $versionLabel = __('N/A');
        }

        return __("Edit Page '%1' Version '%2'", $title, $versionLabel);
    }

    /**
     * Get URL for back button
     *
     * @return string
     */
    public function getBackUrl()
    {
        $cmsPage = $this->_coreRegistry->registry('cms_page');
        return $this->getUrl(
            'adminhtml/cms_page/edit',
            ['page_id' => $cmsPage ? $cmsPage->getId() : null, 'tab' => 'versions']
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
     * Get URL for new button
     *
     * @return string
     */
    public function getNewUrl()
    {
        return $this->getUrl('adminhtml/*/new', ['_current' => true]);
    }

    /**
     * Get Url for new revision button
     *
     * @return string
     */
    public function getNewRevisionUrl()
    {
        return $this->getUrl('adminhtml/cms_page_revision/new', ['_current' => true]);
    }
}
