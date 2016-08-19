<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin\Store;

class Group extends AbstractPlugin
{
    /**
     * Validate changes for invalidating indexer
     *
     * @param \Magento\Framework\Model\AbstractModel $group
     * @return bool
     */
    protected function validate(\Magento\Framework\Model\AbstractModel $group)
    {
        return ($group->dataHasChangedFor(
            'website_id'
        ) || $group->dataHasChangedFor(
            'root_category_id'
        )) && !$group->isObjectNew();
    }

    /**
     * Invalidate indexer on store group save
     *
     * @param \Magento\Store\Model\ResourceModel\Group $subject
     * @param callable $proceed
     * @param \Magento\Framework\Model\AbstractModel $store
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Store\Model\ResourceModel\Group $subject,
        \Closure $proceed,
        \Magento\Framework\Model\AbstractModel $store
    ) {
        $needInvalidating = $this->validate($store);
        $objectResource = $proceed($store);
        if ($needInvalidating && $this->appConfig->isEnabled()) {
            $this->indexerRegistry->get(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID)->invalidate();
        }

        return $objectResource;
    }
}
