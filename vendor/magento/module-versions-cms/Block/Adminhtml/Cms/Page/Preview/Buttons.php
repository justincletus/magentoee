<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Preview;

/**
 * Tool block with buttons
 */
class Buttons extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        array $data = []
    ) {
        $this->_cmsConfig = $cmsConfig;
        parent::__construct($context, $data);
    }

    /**
     * Adding two main buttons
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->buttonList->add(
            'preview',
            [
                'id' => 'preview-buttons-preview',
                'label' => 'Preview',
                'class' => 'preview',
                'onclick' => 'preview()'
            ],
            0,
            0,
            null
        );

        if ($this->_cmsConfig->canCurrentUserPublishRevision()) {
            $this->buttonList->add(
                'publish',
                [
                    'id' => 'preview-buttons-publish',
                    'label' => 'Publish',
                    'class' => 'publish',
                    'onclick' => 'publish()'
                ],
                0,
                0,
                null
            );
        }
    }

    /**
     * Override parent method to produce only button's html in result
     *
     * @return string
     */
    protected function _toHtml()
    {
        parent::_toHtml();
        return $this->getButtonsHtml();
    }
}
