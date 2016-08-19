<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\PromotionPermissions\Block\Adminhtml\Promo\Catalog;

/**
 * @codeCoverageIgnore
 */
class Plugin extends \Magento\PromotionPermissions\Block\Adminhtml\Plugin
{
    /**
     * @var string[]
     */
    protected $restrictedButtons = [
        'add', 'apply_rules',
    ];

    /**
     * @param \Magento\PromotionPermissions\Helper\Data $promoPermData
     */
    public function __construct(\Magento\PromotionPermissions\Helper\Data $promoPermData)
    {
        $this->canEdit = $promoPermData->getCanAdminEditCatalogRules();
    }

    /**
     * Check where button can be rendered
     *
     * @param \Magento\CatalogRule\Block\Adminhtml\Promo\Catalog $subject
     * @param callable $proceed
     * @param \Magento\Backend\Block\Widget\Button\Item $item
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCanRender(
        \Magento\CatalogRule\Block\Adminhtml\Promo\Catalog $subject,
        \Closure $proceed,
        \Magento\Backend\Block\Widget\Button\Item $item
    ) {
        return $this->canRender($subject, $proceed, $item);
    }
}
