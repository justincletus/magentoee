<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Api;

/**
 * CMS Revision CRUD interface.
 */
interface PageRevisionRepositoryInterface
{
    /**
     * Save page revision.
     *
     * @param \Magento\VersionsCms\Api\Data\PageRevisionInterface $pageRevision
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magento\VersionsCms\Api\Data\PageRevisionInterface $pageRevision);

    /**
     * Retrieve page revision.
     *
     * @param int $pageRevisionId
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($pageRevisionId);

    /**
     * Retrieve page revisions matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\VersionsCms\Api\Data\PageRevisionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete page revision.
     *
     * @param \Magento\VersionsCms\Api\Data\PageRevisionInterface $pageRevision
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magento\VersionsCms\Api\Data\PageRevisionInterface $pageRevision);

    /**
     * Delete page revision by ID.
     *
     * @param int $pageRevisionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($pageRevisionId);
}
