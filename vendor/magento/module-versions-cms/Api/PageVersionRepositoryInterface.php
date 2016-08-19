<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Api;

/**
 * CMS Version CRUD interface.
 */
interface PageVersionRepositoryInterface
{
    /**
     * Save page version.
     *
     * @param \Magento\VersionsCms\Api\Data\PageVersionInterface $pageVersion
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magento\VersionsCms\Api\Data\PageVersionInterface $pageVersion);

    /**
     * Retrieve page version.
     *
     * @param int $versionId
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($versionId);

    /**
     * Retrieve page versions matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\VersionsCms\Api\Data\PageVersionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete page version.
     *
     * @param \Magento\VersionsCms\Api\Data\PageVersionInterface $pageVersion
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magento\VersionsCms\Api\Data\PageVersionInterface $pageVersion);

    /**
     * Delete page version by ID.
     *
     * @param int $versionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($versionId);
}
