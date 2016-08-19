<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Revision\Edit\Tab;

/**
 * Meta tab with cms page attributes and some modifications to CE version
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Meta extends \Magento\Cms\Block\Adminhtml\Page\Edit\Tab\Meta
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
     * @param \Magento\VersionsCms\Helper\Data $cmsData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\VersionsCms\Helper\Data $cmsData,
        array $data = []
    ) {
        $this->_cmsData = $cmsData;
        parent::__construct($context, $registry, $formFactory, $data);
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
