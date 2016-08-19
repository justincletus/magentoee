<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

class GroupRepository
{
    /** @var \Magento\Framework\Indexer\IndexerRegistry */
    protected $indexerRegistry;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface
     */
    protected $appConfig;

    /**
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     * @param \Magento\CatalogPermissions\App\ConfigInterface $appConfig
     */
    public function __construct(
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\CatalogPermissions\App\ConfigInterface $appConfig
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->appConfig = $appConfig;
    }

    /**
     * Invalidate indexer on customer group save
     *
     * @param \Magento\Customer\Api\GroupRepositoryInterface $subject
     * @param \Closure $proceed
     * @param \Magento\Customer\Api\Data\GroupInterface $customerGroup
     * @return int
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Customer\Api\GroupRepositoryInterface $subject,
        \Closure $proceed,
        \Magento\Customer\Api\Data\GroupInterface $customerGroup
    ) {
        $needInvalidating = !$customerGroup->getId();

        $customerGroupId = $proceed($customerGroup);

        if ($needInvalidating && $this->appConfig->isEnabled()) {
            $this->indexerRegistry->get(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID)->invalidate();
        }

        return $customerGroupId;
    }

    /**
     * Invalidate indexer on customer group delete
     *
     * @param \Magento\Customer\Api\GroupRepositoryInterface $subject
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDelete(\Magento\Customer\Api\GroupRepositoryInterface $subject)
    {
        return $this->invalidateIndexer();
    }

    /**
     * Invalidate indexer on customer group delete
     *
     * @param \Magento\Customer\Api\GroupRepositoryInterface $subject
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDeleteById(\Magento\Customer\Api\GroupRepositoryInterface $subject)
    {
        return $this->invalidateIndexer();
    }

    /**
     * Invalidate indexer
     *
     * @return bool
     */
    protected function invalidateIndexer()
    {
        if ($this->appConfig->isEnabled()) {
            $this->indexerRegistry->get(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID)->invalidate();
        }
        return true;
    }
}
