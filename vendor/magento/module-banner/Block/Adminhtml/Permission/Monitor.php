<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Banner Permission Monitor block
 *
 * Removes certain blocks from layout if user do not have required permissions
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Banner\Block\Adminhtml\Permission;

class Monitor extends \Magento\Backend\Block\Template
{
    /**
     * Preparing layout
     *
     * @return \Magento\Banner\Block\Adminhtml\Permission\Monitor
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if (!$this->_authorization->isAllowed('Magento_Banner::magento_banner')) {
            /** @var $layout \Magento\Framework\View\LayoutInterface */
            $layout = $this->getLayout();
            if ($layout->getBlock('salesrule.related.banners') !== false) {
                /** @var $promoQuoteBlock \Magento\Backend\Block\Widget\Tabs */
                $promoQuoteBlock = $layout->getBlock('promo_quote_edit_tabs');
                if ($promoQuoteBlock !== false) {
                    $promoQuoteBlock->removeTab('banners_section');
                    $layout->unsetElement('salesrule.related.banners');
                }
            } elseif ($layout->getBlock('catalogrule.related.banners') !== false) {
                /** @var $promoCatalogBlock \Magento\Backend\Block\Widget\Tabs */
                $promoCatalogBlock = $layout->getBlock('promo_catalog_edit_tabs');
                if ($promoCatalogBlock !== false) {
                    $promoCatalogBlock->removeTab('banners_section');
                    $layout->unsetElement('catalogrule.related.banners');
                }
            }
        }
        return $this;
    }
}
