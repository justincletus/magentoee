<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PricePermissions\Observer;

use Magento\Backend\Block\Template;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Backend\Block\Widget\Grid;
use Magento\Framework\Event\ObserverInterface;

class AdminhtmlCatalogProductEditPrepareFormObserver implements ObserverInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var ObserverData
     */
    protected $observerData;

    /**
     * @param \Magento\Framework\Registry $coreRegistry
     * @param ObserverData $observerData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        ObserverData $observerData,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->observerData = $observerData;
        if (isset($data['can_edit_product_status']) && false === $data['can_edit_product_status']) {
            $this->observerData->setCanEditProductStatus(false);
        }
    }

    /**
     * Handle adminhtml_catalog_product_edit_prepare_form event
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->_coreRegistry->registry('product');
        if ($product->isObjectNew()) {
            $form = $observer->getEvent()->getForm();
            // Disable Status drop-down if needed
            if (!$this->observerData->isCanEditProductStatus()) {
                $statusElement = $form->getElement('status');
                if ($statusElement !== null) {
                    $statusElement->setValue(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                    $statusElement->setReadonly(true, true);
                }
            }
        }
    }
}
