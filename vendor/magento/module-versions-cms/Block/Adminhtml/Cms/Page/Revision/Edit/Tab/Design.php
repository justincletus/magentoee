<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Revision\Edit\Tab;

/**
 * Design tab with cms page attributes and some modifications to CE version
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Design extends \Magento\Cms\Block\Adminhtml\Page\Edit\Tab\Design
{
    /**
     * Cms data
     *
     * @var \Magento\VersionsCms\Helper\Data
     */
    protected $_cmsData = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Theme\Model\Layout\Source\Layout $pageLayout
     * @param \Magento\Framework\View\Design\Theme\LabelFactory $labelFactory
     * @param \Magento\VersionsCms\Helper\Data $cmsData
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Theme\Model\Layout\Source\Layout $pageLayout,
        \Magento\Framework\View\Design\Theme\LabelFactory $labelFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        \Magento\VersionsCms\Helper\Data $cmsData,
        array $data = []
    ) {
        $this->_cmsData = $cmsData;
        parent::__construct($context, $registry, $formFactory, $pageLayout, $labelFactory, $pageLayoutBuilder, $data);
    }

    /**
     * Adding onchange js call
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $this->_cmsData->addAttributeToFormElements('data-role', 'cms-revision-form-changed', $this->getForm());
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
