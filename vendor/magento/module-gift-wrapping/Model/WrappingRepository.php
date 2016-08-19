<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftWrapping\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\GiftWrapping\Api\WrappingRepositoryInterface;
use Magento\GiftWrapping\Api\Data\WrappingSearchResultsInterfaceFactory;
use Magento\Framework\Exception\StateException;
use Magento\Store\Model\Store;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WrappingRepository implements WrappingRepositoryInterface
{
    /**
     * @var \Magento\GiftWrapping\Model\WrappingFactory
     */
    protected $wrappingFactory;

    /**
     * @var \Magento\GiftWrapping\Model\ResourceModel\Wrapping\CollectionFactory
     */
    protected $wrappingCollectionFactory;

    /**
     * @var WrappingSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\GiftWrapping\Model\ResourceModel\Wrapping
     */
    protected $resourceModel;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param WrappingFactory $wrappingFactory
     * @param \Magento\GiftWrapping\Model\ResourceModel\Wrapping\CollectionFactory $wrappingCollectionFactory
     * @param WrappingSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\GiftWrapping\Model\ResourceModel\Wrapping $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\GiftWrapping\Model\WrappingFactory $wrappingFactory,
        \Magento\GiftWrapping\Model\ResourceModel\Wrapping\CollectionFactory $wrappingCollectionFactory,
        WrappingSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\GiftWrapping\Model\ResourceModel\Wrapping $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->wrappingFactory = $wrappingFactory;
        $this->wrappingCollectionFactory = $wrappingCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->resourceModel = $resource;
        $this->storeManager = $storeManager;
    }

    /**
     * Load wrapping model for specified store
     *
     * @param int $id
     * @param int $storeId
     * @return \Magento\GiftWrapping\Model\Wrapping
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id, $storeId = null)
    {
        /** @var \Magento\GiftWrapping\Model\Wrapping $wrapping */
        $wrapping = $this->wrappingFactory->create();
        $wrapping->setStoreId($storeId);
        $this->resourceModel->load($wrapping, $id);
        if (!$wrapping->getId()) {
            throw new NoSuchEntityException(__('Gift Wrapping with specified ID "%1" not found.', $id));
        }
        $wrapping->setWebsiteIds($wrapping->getWebsiteIds());
        $wrapping->setImageName($wrapping->getImage());
        $wrapping->setImageUrl($wrapping->getImageUrl());
        return $wrapping;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        /** @var  \Magento\GiftWrapping\Model\ResourceModel\Wrapping\Collection $collection */
        $collection = $this->wrappingCollectionFactory->create();
        $collection->addWebsitesToResult();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == 'status' && $filter->getValue()) {
                    $collection->applyStatusFilter();
                } elseif ($filter->getField() == 'store_id') {
                    $collection->addStoreAttributesToResult((int)$filter->getValue());
                } else {
                    $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                    $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
                }
            }
        }

        return $this->searchResultsFactory->create()
            ->setItems($collection->getItems())
            ->setTotalCount($collection->getSize())
            ->setSearchCriteria($searchCriteria);
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\GiftWrapping\Api\Data\WrappingInterface $data, $storeId = null)
    {
        $id = $data->getWrappingId();
        $currencyCode = $data->getBaseCurrencyCode();
        $baseCurrencyCode = $this->storeManager->getStore()->getBaseCurrencyCode();
        if (isset($currencyCode) && ($currencyCode != $baseCurrencyCode)) {
            throw new StateException(__('Please enter valid currency code: %1', $baseCurrencyCode));
        }
        if ($id) {
            $data = $this->get($id)->addData($data->getData());
        }
        $imageContent = base64_decode($data->getImageBase64Content(), true);
        if ($storeId === null) {
            $storeId = Store::DEFAULT_STORE_ID;
        }
        $data->setStoreId($storeId);
        $data->attachBinaryImage($data->getImageName(), $imageContent);
        $this->resourceModel->save($data);
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\GiftWrapping\Api\Data\WrappingInterface $data)
    {
        try {
            $this->resourceModel->delete($data);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Unable to remove gift wrapping %1', $data->getWrappingId())
            );
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($id)
    {
        $model = $this->get($id);
        $this->delete($model);
        return true;
    }
}
