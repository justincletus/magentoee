<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Model\Page;

use Magento\VersionsCms\Api\Data\PageRevisionInterface;
use Magento\VersionsCms\Api\Data\PageRevisionInterfaceFactory;
use Magento\VersionsCms\Api\Data\PageRevisionSearchResultsInterfaceFactory;
use Magento\VersionsCms\Api\PageRevisionRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\VersionsCms\Model\ResourceModel\Page\Revision as ResourceRevision;
use Magento\VersionsCms\Model\ResourceModel\Page\Revision\Collection;
use Magento\VersionsCms\Model\ResourceModel\Page\Revision\CollectionFactory;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class RevisionRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RevisionRepository implements PageRevisionRepositoryInterface
{
    /**
     * @var ResourceRevision
     */
    protected $resource;

    /**
     * @var RevisionFactory
     */
    protected $revisionFactory;

    /**
     * @var CollectionFactory
     */
    protected $revisionCollectionFactory;

    /**
     * @var PageRevisionSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var PageRevisionInterfaceFactory
     */
    protected $dataRevisionFactory;

    /**
     * @param ResourceRevision $resource
     * @param RevisionFactory $revisionFactory
     * @param PageRevisionInterfaceFactory $dataRevisionFactory
     * @param CollectionFactory $revisionCollectionFactory
     * @param PageRevisionSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        ResourceRevision $resource,
        RevisionFactory $revisionFactory,
        PageRevisionInterfaceFactory $dataRevisionFactory,
        CollectionFactory $revisionCollectionFactory,
        PageRevisionSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->revisionFactory = $revisionFactory;
        $this->revisionCollectionFactory = $revisionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataRevisionFactory = $dataRevisionFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Save Revision data
     *
     * @param \Magento\VersionsCms\Api\Data\PageRevisionInterface $revision
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     * @throws CouldNotSaveException
     */
    public function save(PageRevisionInterface $revision)
    {
        try {
            $this->resource->save($revision);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $revision;
    }

    /**
     * Load Revision data by given Revision Identity
     *
     * @param string $revisionId
     * @return PageRevisionInterface
     * @throws NoSuchEntityException
     */
    public function getById($revisionId)
    {
        $revision = $this->revisionFactory->create();
        $this->resource->load($revision, $revisionId);
        if (!$revision->getId()) {
            throw new NoSuchEntityException(__('CMS Revision with id "%1" does not exist.', $revisionId));
        }
        return $revision;
    }

    /**
     * Load Revision data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return Collection
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->revisionCollectionFactory->create();
        $collection->joinVersions();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'version_id') {
                    $collection->addVersionFilter($filter->getValue());
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $revisions = [];
        /** @var Revision $revisionModel */
        foreach ($collection as $revisionModel) {
            $revisionData = $this->revisionFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $revisionData,
                $revisionModel->getData(),
                'Magento\VersionsCms\Api\Data\PageRevisionInterface'
            );
            $revisions[] = $this->dataObjectProcessor->buildOutputDataArray(
                $revisionData,
                'Magento\VersionsCms\Api\Data\PageVersionInterface'
            );
        }
        $searchResults->setItems($revisions);
        return $searchResults;
    }

    /**
     * Delete Revision
     *
     * @param PageRevisionInterface $revision
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\Magento\VersionsCms\Api\Data\PageRevisionInterface $revision)
    {
        try {
            $this->resource->delete($revision);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Revision by given Revision Identity
     *
     * @param string $revisionId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($revisionId)
    {
        return $this->delete($this->getById($revisionId));
    }
}
