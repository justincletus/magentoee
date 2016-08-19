<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page;

/**
 * Cms page edit form revisions tab
 */
class Edit extends \Magento\Backend\Block\Template
{
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
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_cmsConfig = $cmsConfig;
        parent::__construct($context, $data);
    }

    /**
     * Adding js to CE blocks to implement special functionality which
     * will allow go back to edit page with pre loaded tab passed through query string.
     * Added permission checking to remove some buttons if needed.
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _prepareLayout()
    {
        $tabsBlock = $this->getLayout()->getBlock('cms_page_edit_tabs');
        /* @var $tabBlock \Magento\Cms\Block\Adminhtml\Page\Edit\Tabs */
        if ($tabsBlock) {
            $editBlock = $this->getLayout()->getBlock('cms_page_edit');
            /* @var $editBlock \Magento\Cms\Block\Adminhtml\Page\Edit */
            if ($editBlock) {
                $page = $this->_coreRegistry->registry('cms_page');
                if ($page) {
                    if ($page->getId()) {
                        $this->getToolbar()->addChild(
                            'preview',
                            'Magento\Backend\Block\Widget\Button',
                            [
                                'label' => __('Preview'),
                                'class' => 'preview',
                                'data_attribute' => [
                                    'mage-init' => [
                                        'button' => [
                                            'event' => 'preview',
                                            'target' => '#edit_form',
                                            'eventData' => [
                                                'action' => $this->getUrl('adminhtml/cms_page_revision/preview'),
                                            ],
                                        ],
                                    ],
                                ]
                            ]
                        );
                    }

                    $formBlock = $editBlock->getChildBlock('form');
                    if ($formBlock) {
                        $formBlock->setTemplate('Magento_VersionsCms::page/edit/form.phtml');
                        if ($page->getUnderVersionControl()) {
                            $tabId = $this->getRequest()->getParam('tab');
                            if ($tabId) {
                                $formBlock->setSelectedTabId(
                                    $tabsBlock->getId() . '_' . $tabId
                                )->setTabJsObject(
                                    $tabsBlock->getJsObjectName()
                                );
                            }
                        }
                    }
                    // If user non-publisher he can save page only if it has disabled status
                    if ($page->getUnderVersionControl()) {
                        if ($page->getId() && $page->getIsActive() == \Magento\Cms\Model\Page::STATUS_ENABLED) {
                            if (!$this->_cmsConfig->canCurrentUserPublishRevision()) {
                                $editBlock->removeButton('delete');
                                $editBlock->removeButton('save');
                                $editBlock->removeButton('saveandcontinue');
                            }
                        }
                    }
                }
            }
        }
        return parent::_prepareLayout();
    }
}
