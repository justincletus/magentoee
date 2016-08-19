<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Controller\Adminhtml\Banner;

class CatalogRuleBannersGrid extends \Magento\Banner\Controller\Adminhtml\Banner
{
    /**
     * Banner binding tab grid action on catalog rule
     *
     * @return void
     */
    public function execute()
    {
        $ruleId = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magento\CatalogRule\Model\Rule');

        if ($ruleId) {
            $model->load($ruleId);
            if (!$model->getRuleId()) {
                $this->messageManager->addError(__('This rule no longer exists.'));
                $this->_redirect('adminhtml/*');
                return;
            }
        }
        if (!$this->_registry->registry('current_promo_catalog_rule')) {
            $this->_registry->register('current_promo_catalog_rule', $model);
        }
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock(
            'related_catalogrule_banners_grid'
        )->setSelectedCatalogruleBanners(
            $this->getRequest()->getPost('selected_catalogrule_banners')
        );
        $this->_view->renderLayout();
    }
}
