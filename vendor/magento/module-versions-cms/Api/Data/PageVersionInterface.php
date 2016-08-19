<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Api\Data;

/**
 * Page Version interface.
 */
interface PageVersionInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const VERSION_ID      = 'version_id';
    const LABEL           = 'label';
    const ACCESS_LEVEL    = 'access_level';
    const REVISIONS_COUNT = 'revisions_count';
    const VERSION_NUMBER  = 'version_number';
    const CREATED_AT      = 'created_at';
    const PAGE_ID         = 'page_id';
    const USER_ID         = 'user_id';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get label
     *
     * @return string|null
     */
    public function getLabel();

    /**
     * Get access level
     *
     * @return string|null
     */
    public function getAccessLevel();

    /**
     * Get revisions count
     *
     * @return int|null
     */
    public function getRevisionsCount();

    /**
     * Get version number
     *
     * @return int|null
     */
    public function getVersionNumber();

    /**
     * Get created at
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get page ID
     *
     * @return int
     */
    public function getPageId();

    /**
     * Get user ID
     *
     * @return int
     */
    public function getUserId();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     */
    public function setId($id);

    /**
     * Set label
     *
     * @param string $label
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     */
    public function setLabel($label);

    /**
     * Set access level
     *
     * @param string $accessLevel
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     */
    public function setAccessLevel($accessLevel);

    /**
     * Set revisions count
     *
     * @param int $revisionsCount
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     */
    public function setRevisionsCount($revisionsCount);

    /**
     * Set version number
     *
     * @param int $versionNumber
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     */
    public function setVersionNumber($versionNumber);

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Set page ID
     *
     * @param int $pageId
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     */
    public function setPageId($pageId);

    /**
     * Set user ID
     *
     * @param int $userId
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     */
    public function setUserId($userId);
}
