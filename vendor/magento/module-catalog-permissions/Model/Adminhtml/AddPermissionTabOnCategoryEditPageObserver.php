<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Adminhtml observer
 *
 */
namespace Magento\CatalogPermissions\Model\Adminhtml;

use Magento\Catalog\Block\Adminhtml\Category\Tabs;
use Magento\Catalog\Model\Category;
use Magento\CatalogPermissions\App\ConfigInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddPermissionTabOnCategoryEditPageObserver implements ObserverInterface
{
    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface
     */
    protected $appConfig;

    /**
     * @param AuthorizationInterface $authorization
     * @param ConfigInterface $appConfig
     */
    public function __construct(AuthorizationInterface $authorization, ConfigInterface $appConfig)
    {
        $this->appConfig = $appConfig;
        $this->authorization = $authorization;
    }

    /**
     * Add permission tab on category edit page
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->appConfig->isEnabled()) {
            return $this;
        }
        if (!$this->authorization->isAllowed('Magento_CatalogPermissions::catalog_magento_catalogpermissions')) {
            return $this;
        }

        $tabs = $observer->getEvent()->getTabs();
        /* @var $tabs Tabs */

        $tabs->addTab('permissions', 'Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions');

        return $this;
    }
}
