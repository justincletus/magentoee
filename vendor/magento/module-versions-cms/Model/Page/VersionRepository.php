<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Model\Page;

use Magento\VersionsCms\Api\Data\PageVersionInterface;
use Magento\VersionsCms\Api\Data\PageVersionInterfaceFactory;
use Magento\VersionsCms\Api\Data\PageVersionSearchResultsInterfaceFactory;
use Magento\VersionsCms\Api\PageVersionRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\VersionsCms\Model\ResourceModel\Page\Version as ResourceVersion;
use Magento\VersionsCms\Model\ResourceModel\Page\Version\Collection;
use Magento\VersionsCms\Model\ResourceModel\Page\Version\CollectionFactory;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class VersionRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class VersionRepository implements PageVersionRepositoryInterface
{
    /**
     * @var ResourceVersion
     */
    protected $resource;

    /**
     * @var VersionFactory
     */
    protected $versionFactory;

    /**
     * @var CollectionFactory
     */
    protected $versionCollectionFactory;

    /**
     * @var VersionSearchResultsInterfaceFactory
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
     * @var PageVersionInterfaceFactory
     */
    protected $dataVersionFactory;

    /**
     * @param ResourceVersion $resource
     * @param VersionFactory $versionFactory
     * @param PageVersionInterfaceFactory $dataVersionFactory
     * @param CollectionFactory $versionCollectionFactory
     * @param PageVersionSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        ResourceVersion $resource,
        VersionFactory $versionFactory,
        PageVersionInterfaceFactory $dataVersionFactory,
        CollectionFactory $versionCollectionFactory,
        PageVersionSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->versionFactory = $versionFactory;
        $this->versionCollectionFactory = $versionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataVersionFactory = $dataVersionFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Save Version data
     *
     * @param \Magento\VersionsCms\Api\Data\PageVersionInterface $version
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     * @throws CouldNotSaveException
     */
    public function save(\Magento\VersionsCms\Api\Data\PageVersionInterface $version)
    {
        try {
            $this->resource->save($version);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $version;
    }

    /**
     * Load Version data by given Version Identity
     *
     * @param string $versionId
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     * @throws NoSuchEntityException
     */
    public function getById($versionId)
    {
        $version = $this->versionFactory->create();
        $this->resource->load($version, $versionId);
        if (!$version->getId()) {
            throw new NoSuchEntityException(__('CMS Version with id "%1" does not exist.', $versionId));
        }
        return $version;
    }

    /**
     * Load Version data collection by given search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return Collection
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->versionCollectionFactory->create();
        $collection->joinRevisions();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
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
        $versions = [];
        /** @var Version $versionModel */
        foreach ($collection as $versionModel) {
            $versionData = $this->versionFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $versionData,
                $versionModel->getData(),
                'Magento\VersionsCms\Api\Data\PageVersionInterface'
            );
            $versions[] = $this->dataObjectProcessor->buildOutputDataArray(
                $versionData,
                'Magento\VersionsCms\Api\Data\PageVersionInterface'
            );
        }
        $searchResults->setItems($versions);
        return $searchResults;
    }

    /**
     * Delete Version
     *
     * @param \Magento\VersionsCms\Api\Data\PageVersionInterface $version
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\Magento\VersionsCms\Api\Data\PageVersionInterface $version)
    {
        try {
            $this->resource->delete($version);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Version by given Version Identity
     *
     * @param string $versionId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($versionId)
    {
        return $this->delete($this->getById($versionId));
    }
}
