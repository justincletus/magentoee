<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Api\Data;

/**
 * Page Revision interface.
 */
interface PageRevisionInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const REVISION_ID              = 'revision_id';
    const VERSION_ID               = 'version_id';
    const REVISION_NUMBER          = 'revision_number';
    const PAGE_ID                  = 'page_id';
    const USER_ID                  = 'user_id';
    const PAGE_LAYOUT              = 'page_layout';
    const META_KEYWORDS            = 'meta_keywords';
    const META_DESCRIPTION         = 'meta_description';
    const CONTENT_HEADING          = 'content_heading';
    const CONTENT                  = 'content';
    const CREATED_AT               = 'created_at';
    const LAYOUT_UPDATE_XML        = 'layout_update_xml';
    const CUSTOM_THEME             = 'custom_theme';
    const CUSTOM_PAGE_LAYOUT       = 'custom_page_layout';
    const CUSTOM_LAYOUT_UPDATE_XML = 'custom_layout_update_xml';
    const CUSTOM_THEME_FROM        = 'custom_theme_from';
    const CUSTOM_THEME_TO          = 'custom_theme_to';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get version ID
     *
     * @return int
     */
    public function getVersionId();

    /**
     * Get revision number
     *
     * @return int|null
     */
    public function getRevisionNumber();

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
     * Get page layout
     *
     * @return string|null
     */
    public function getPageLayout();

    /**
     * Get meta keywords
     *
     * @return string|null
     */
    public function getMetaKeywords();

    /**
     * Get meta description
     *
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Get content heading
     *
     * @return string|null
     */
    public function getContentHeading();

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent();

    /**
     * Get created at
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get layout update xml
     *
     * @return string|null
     */
    public function getLayoutUpdateXml();

    /**
     * Get custom theme
     *
     * @return string|null
     */
    public function getCustomTheme();

    /**
     * Get custom page layout
     *
     * @return string|null
     */
    public function getCustomPageLayout();

    /**
     * Get custom layout update xml
     *
     * @return string|null
     */
    public function getCustomLayoutUpdateXml();

    /**
     * Get custom theme from
     *
     * @return string|null
     */
    public function getCustomThemeFrom();

    /**
     * Get custom theme to
     *
     * @return string|null
     */
    public function getCustomThemeTo();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setId($id);

    /**
     * Set version ID
     *
     * @param int $versionId
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setVersionId($versionId);

    /**
     * Set revision number
     *
     * @param int $revisionNumber
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setRevisionNumber($revisionNumber);

    /**
     * Set page ID
     *
     * @param int $pageId
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setPageId($pageId);

    /**
     * Set user ID
     *
     * @param int $userId
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setUserId($userId);

    /**
     * Set page layout
     *
     * @param string $pageLayout
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setPageLayout($pageLayout);

    /**
     * Set meta keywords
     *
     * @param string $metaKeywords
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setMetaKeywords($metaKeywords);

    /**
     * Set meta description
     *
     * @param string $metaDescription
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setMetaDescription($metaDescription);

    /**
     * Set content heading
     *
     * @param string $contentHeading
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setContentHeading($contentHeading);

    /**
     * Set content
     *
     * @param string $content
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setContent($content);

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Set layout update xml
     *
     * @param string $layoutUpdateXml
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setLayoutUpdateXml($layoutUpdateXml);

    /**
     * Set custom theme
     *
     * @param string $customTheme
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setCustomTheme($customTheme);

    /**
     * Set custom page layout
     *
     * @param string $customPageLayout
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setCustomPageLayout($customPageLayout);

    /**
     * Set custom layout update xml
     *
     * @param string $customLayoutUpdateXml
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setCustomLayoutUpdateXml($customLayoutUpdateXml);

    /**
     * Set custom theme from
     *
     * @param string $customThemeFrom
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setCustomThemeFrom($customThemeFrom);

    /**
     * Set custom theme to
     *
     * @param string $customThemeTo
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setCustomThemeTo($customThemeTo);
}
