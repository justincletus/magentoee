<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Model\Indexer;

class Category implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'catalogpermissions_category';

    /**
     * @var Category\Action\FullFactory
     */
    protected $fullActionFactory;

    /**
     * @var Category\Action\RowsFactory
     */
    protected $rowsActionFactory;

    /** @var \Magento\Framework\Indexer\IndexerRegistry */
    protected $indexerRegistry;

    /**
     * @param Category\Action\FullFactory $fullActionFactory
     * @param Category\Action\RowsFactory $rowsActionFactory
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     */
    public function __construct(
        Category\Action\FullFactory $fullActionFactory,
        Category\Action\RowsFactory $rowsActionFactory,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    ) {
        $this->fullActionFactory = $fullActionFactory;
        $this->rowsActionFactory = $rowsActionFactory;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $this->fullActionFactory->create()->execute();
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @return void
     */
    public function executeList(array $ids)
    {
        $this->executeAction($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @return void
     */
    public function executeRow($id)
    {
        $this->executeAction([$id]);
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     */
    public function execute($ids)
    {
        $this->executeAction($ids);
    }

    /**
     * Execute action for single entity or list of entities
     *
     * @param int[] $ids
     * @return void
     */
    protected function executeAction($ids)
    {
        $ids = array_unique($ids);

        /** @var Category\Action\Rows $action */
        $action = $this->rowsActionFactory->create();
        if ($this->indexerRegistry->get(static::INDEXER_ID)->isWorking()) {
            $action->execute($ids, true);
        }
        $action->execute($ids);
    }
}
