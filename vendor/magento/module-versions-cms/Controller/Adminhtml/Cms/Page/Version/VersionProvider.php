<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version;

class VersionProvider
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\VersionsCms\Model\Page\VersionFactory
     */
    protected $_pageVersionFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\VersionsCms\Model\Page\VersionFactory $pageVersionFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\VersionsCms\Model\Page\VersionFactory $pageVersionFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\VersionsCms\Model\Config $cmsConfig
    ) {
        $this->_coreRegistry = $registry;
        $this->_pageVersionFactory = $pageVersionFactory;
        $this->_backendAuthSession = $authSession;
        $this->_cmsConfig = $cmsConfig;
    }

    /**
     * Retrieve version by id
     *
     * @param string $versionId
     * @return \Magento\VersionsCms\Model\Page\Version
     */
    public function get($versionId)
    {
        $version = $this->_pageVersionFactory->create();
        /* @var $version \Magento\VersionsCms\Model\Page\Version */

        if ($versionId) {
            $userId = $this->_backendAuthSession->getUser()->getId();
            $accessLevel = $this->_cmsConfig->getAllowedAccessLevel();
            $version->loadWithRestrictions($accessLevel, $userId, $versionId);
        }

        $this->_coreRegistry->register('cms_page_version', $version);
        return $version;
    }
}
