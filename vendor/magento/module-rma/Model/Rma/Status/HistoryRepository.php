<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Model\Rma\Status;

/**
 * Class HistoryRepository
 * Repository class for \Magento\Rma\Model\Rma\Status\History
 */
class HistoryRepository
{
    /**
     * historyFactory
     *
     * @var \Magento\Rma\Model\Rma\Status\HistoryFactory
     */
    protected $historyFactory = null;

    /**
     * Collection Factory
     *
     * @var \Magento\Rma\Model\ResourceModel\Rma\Status\History\CollectionFactory
     */
    protected $historyCollectionFactory = null;

    /**
     * Magento\Rma\Model\Rma\Status\History[]
     *
     * @var array
     */
    protected $registry = [];

    /**
     * Repository constructor
     *
     * @param \Magento\Rma\Model\Rma\Status\History $historyFactory
     * @param \Magento\Rma\Model\ResourceModel\Rma\Status\History\CollectionFactory
     * $historyCollectionFactory
     */
    public function __construct(
        \Magento\Rma\Model\Rma\Status\HistoryFactory $historyFactory,
        \Magento\Rma\Model\ResourceModel\Rma\Status\History\CollectionFactory $historyCollectionFactory
    ) {
        $this->historyFactory = $historyFactory;
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    /**
     * load entity
     *
     * @param int $id
     * @return \Magento\Rma\Model\Rma\Status\History
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        if (empty($id)) {
            throw new \Magento\Framework\Exception\InputException(__('ID cannot be an empty'));
        }
        if (!isset($this->registry[$id])) {
            $entity = $this->historyFactory->create()->load($id);
            if (!$entity->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('Requested entity doesn\'t exist')
                );
            }
            $this->registry[$id] = $entity;
        }
        return $this->registry[$id];
    }

    /**
     * Register entity
     *
     * @param \Magento\Rma\Model\Rma\Status\History $object
     * @return \Magento\Rma\Model\Rma\Status\HistoryRepository
     */
    public function register(\Magento\Rma\Model\Rma\Status\History $object)
    {
        if ($object->getId() && !isset($this->registry[$object->getId()])) {
            $object->load($object->getId());
            $this->registry[$object->getId()] = $object;
        }
        return $this;
    }

    /**
     * Find entities by criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $criteria
     * @return \Magento\Rma\Model\Rma\Status\History[]
     */
    public function find(\Magento\Framework\Api\SearchCriteria $criteria)
    {
        $collection = $this->historyCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        foreach ($collection as $object) {
            $this->register($object);
        }
        $objectIds = $collection->getAllIds();
        return array_intersect_key($this->registry, array_flip($objectIds));
    }
}
