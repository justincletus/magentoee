<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms;

/**
 * Adminhtml cms pages content block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Page extends \Magento\Backend\Block\Template
{
    /**
     * Add  column Versioned to cms page grid
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        /* @var $pageGrid \Magento\Cms\Block\Adminhtml\Page\Grid */
        $page = $this->getLayout()->getBlock('cms_page');
        if ($page) {
            $pageGrid = $page->getChildBlock('grid');
            if ($pageGrid) {
                $pageGrid->addColumnAfter(
                    'versioned',
                    [
                        'index' => 'under_version_control',
                        'header' => __('Version Control'),
                        'type' => 'options',
                        'options' => [__('No'), __('Yes')]
                    ],
                    'page_actions'
                );
            }
        }

        return $this;
    }
}
