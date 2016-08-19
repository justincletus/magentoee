<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\VersionsCms\Model;

/**
 * Enterprise cms page config model
 */
class Config
{
    const XML_PATH_CONTENT_VERSIONING = 'cms/content/versioning';

    /**
     * @var array
     */
    protected $_revisionControlledAttributes = [
        'page' => [
            'page_layout',
            'meta_keywords',
            'meta_description',
            'content_heading',
            'content',
            'layout_update_xml',
            'custom_theme',
            'custom_page_layout',
            'custom_layout_update_xml',
            'custom_theme_from',
            'custom_theme_to',
        ],
    ];

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     */
    public function __construct(
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Backend\Model\Auth\Session $backendAuthSession
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_authorization = $authorization;
        $this->_backendAuthSession = $backendAuthSession;
    }

    /**
     * Retrieves attributes for passed cms
     * type excluded from revision control.
     *
     * @param string $type
     * @return array
     */
    protected function _getRevisionControledAttributes($type)
    {
        if (isset($this->_revisionControlledAttributes[$type])) {
            return $this->_revisionControlledAttributes[$type];
        }
        return [];
    }

    /**
     * Retrieves cms page's attributes which are under revision control.
     *
     * @return array
     */
    public function getPageRevisionControledAttributes()
    {
        return $this->_getRevisionControledAttributes('page');
    }

    /**
     * Returns array of access levels which can be viewed by current user.
     *
     * @return string|string[]
     */
    public function getAllowedAccessLevel()
    {
        if ($this->canCurrentUserPublishRevision()) {
            return [
                \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PROTECTED,
                \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PUBLIC
            ];
        } else {
            return [\Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PUBLIC];
        }
    }

    /**
     * Returns status of current user publish permission.
     *
     * @return bool
     */
    public function canCurrentUserPublishRevision()
    {
        return $this->_authorization->isAllowed('Magento_VersionsCms::publish_revision');
    }

    /**
     * Return status of current user delete page permission.
     *
     * @return bool
     */
    public function canCurrentUserDeletePage()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page_delete');
    }

    /**
     * Return status of current user create new page permission.
     *
     * @return bool
     */
    public function canCurrentUserSavePage()
    {
        return $this->_authorization->isAllowed('Magento_Cms::save');
    }

    /**
     * Return status of current user permission to save revision.
     *
     * @return bool
     */
    public function canCurrentUserSaveRevision()
    {
        return $this->_authorization->isAllowed('Magento_VersionsCms::save_revision');
    }

    /**
     * Return status of current user permission to delete revision.
     *
     * @return bool
     */
    public function canCurrentUserDeleteRevision()
    {
        return $this->_authorization->isAllowed('Magento_VersionsCms::delete_revision');
    }

    /**
     * Return status of current user permission to save version.
     *
     * @return bool
     */
    public function canCurrentUserSaveVersion()
    {
        return $this->canCurrentUserSaveRevision();
    }

    /**
     * Return status of current user permission to delete version.
     *
     * @return bool
     */
    public function canCurrentUserDeleteVersion()
    {
        return $this->canCurrentUserDeleteRevision();
    }

    /**
     * Compare current user with passed owner of version or author of revision.
     *
     * @param int $userId
     * @return bool
     */
    public function isCurrentUserOwner($userId)
    {
        return $this->_backendAuthSession->getUser()->getId() == $userId;
    }

    /**
     * Get default value for versioning from configuration.
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getDefaultVersioningStatus()
    {
        return $this->_scopeConfig->isSetFlag(self::XML_PATH_CONTENT_VERSIONING, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
