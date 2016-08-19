<?php
/***
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Block\Adminhtml\Category;

/**
 * Replace the existing tab with a new Visual Merchadiser tab
 * @package Magento\VisualMerchandiser\Block\Adminhtml\Category\Plugin
 */
class Plugin
{
    /**
     * Before HTML output, remove the product tab and replace it with VisualMerchandiser tab
     *
     * @param \Magento\Catalog\Block\Adminhtml\Category\Tabs $subject
     * @return void
     */
    public function beforeToHtml(\Magento\Catalog\Block\Adminhtml\Category\Tabs $subject)
    {
        $subject->removeTab('products');
        $subject->addTab(
            'merchandiser',
            [
                'label' => __('Category Products'),
                'content' => $subject->getBlockHtml('category.merchandiser.container')
            ]
        );
    }
}
