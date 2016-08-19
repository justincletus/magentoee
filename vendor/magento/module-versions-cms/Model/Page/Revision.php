<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Model\Page;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\VersionsCms\Api\Data\PageRevisionInterface;

/**
 * Cms page revision model
 *
 * @method \Magento\VersionsCms\Model\ResourceModel\Page\Revision _getResource()
 * @method \Magento\VersionsCms\Model\ResourceModel\Page\Revision getResource()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Revision extends AbstractModel implements IdentityInterface, \Magento\VersionsCms\Api\Data\PageRevisionInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'CMS_REVISION';

    /**
     * Preview uri
     */
    const PREVIEW_URI = 'versionscms/page_revision/drop/';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'magento_versionscms_revision';

    /**
     * Parameter name in event.
     * In observe method you can use $observer->getEvent()->getObject() in this case.
     *
     * @var string
     */
    protected $_eventObject = 'revision';

    /**
     * Configuration model
     *
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_config;

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_coreDate;

    /**
     * @var \Magento\VersionsCms\Model\IncrementFactory
     */
    protected $_cmsIncrementFactory;

    /**
     * @var \Magento\VersionsCms\Model\Page\RevisionFactory
     */
    protected $_pageRevisionFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     * @param \Magento\VersionsCms\Model\IncrementFactory $cmsIncrementFactory
     * @param \Magento\VersionsCms\Model\Page\RevisionFactory $pageRevisionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Magento\VersionsCms\Model\IncrementFactory $cmsIncrementFactory,
        \Magento\VersionsCms\Model\Page\RevisionFactory $pageRevisionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_config = $cmsConfig;
        $this->_coreDate = $coreDate;
        $this->_cmsIncrementFactory = $cmsIncrementFactory;
        $this->_pageRevisionFactory = $pageRevisionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\VersionsCms\Model\ResourceModel\Page\Revision');
    }

    /**
     * Preparing data before save
     *
     * @return $this
     */
    public function beforeSave()
    {
        /**
         * Resetting revision id this revision should be saved as new.
         * Bc data was changed or original version id not equals to new version id.
         */
        if ($this->_revisionedDataWasModified() || $this->getVersionId() != $this->getOrigData('version_id')) {
            $this->unsetData($this->getIdFieldName());
            $this->setCreatedAt($this->_coreDate->gmtDate());

            $incrementNumber = $this->_cmsIncrementFactory->create()->getNewIncrementId(
                \Magento\VersionsCms\Model\Increment::TYPE_PAGE,
                $this->getVersionId(),
                \Magento\VersionsCms\Model\Increment::LEVEL_REVISION
            );

            $this->setRevisionNumber($incrementNumber);
        }

        return parent::beforeSave();
    }

    /**
     * Check data which is under revision control if it was modified.
     *
     * @return bool
     */
    protected function _revisionedDataWasModified()
    {
        $attributes = $this->_config->getPageRevisionControledAttributes();
        foreach ($attributes as $attr) {
            $value = $this->getData($attr);
            if ($this->getOrigData($attr) !== $value) {
                if ($this->getOrigData($attr) === null && $value === '' || $value === null) {
                    continue;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Prepare data which must be published
     *
     * @return array
     */
    protected function _prepareDataForPublish()
    {
        $data = [];
        $attributes = $this->_config->getPageRevisionControledAttributes();
        foreach ($this->getData() as $key => $value) {
            if (in_array($key, $attributes)) {
                $this->unsetData($key);
                $data[$key] = $value;
            }
        }

        $data['published_revision_id'] = $this->getId();

        return $data;
    }

    /**
     * Publishing current revision
     *
     * @return $this
     * @throws \Exception
     */
    public function publish()
    {
        $this->_getResource()->beginTransaction();
        try {
            $data = $this->_prepareDataForPublish($this);
            $object = $this->_pageRevisionFactory->create()->setData($data);
            $this->_getResource()->publish($object, $this->getPageId());
            $this->_getResource()->commit();
        } catch (\Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        $this->cleanModelCache();
        return $this;
    }

    /**
     * Checking some moments before we can actually delete revision
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDelete()
    {
        $resource = $this->_getResource();
        /* @var $resource \Magento\VersionsCms\Model\ResourceModel\Page\Revision */
        if ($resource->isRevisionPublished($this)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Revision #%1 could not be removed because it is published.', $this->getRevisionNumber())
            );
        }
    }

    /**
     * Loading revision with extra access level checking.
     *
     * @param array|string $accessLevel
     * @param int $userId
     * @param int|string $value
     * @param string|null $field
     * @return $this
     */
    public function loadWithRestrictions($accessLevel, $userId, $value, $field = null)
    {
        $this->_getResource()->loadWithRestrictions($this, $accessLevel, $userId, $value, $field);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Loading revision with empty data which is under
     * control and with other data from version and page.
     * Also apply extra access level checking.
     *
     * @param int $versionId
     * @param int $pageId
     * @param array|string $accessLevel
     * @param int $userId
     * @return $this
     */
    public function loadByVersionPageWithRestrictions($versionId, $pageId, $accessLevel, $userId)
    {
        $this->_getResource()->loadByVersionPageWithRestrictions($this, $versionId, $pageId, $accessLevel, $userId);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::REVISION_ID);
    }

    /**
     * Get version ID
     *
     * @return int
     */
    public function getVersionId()
    {
        return parent::getData(self::VERSION_ID);
    }

    /**
     * Get revision number
     *
     * @return string
     */
    public function getRevisionNumber()
    {
        return parent::getData(self::REVISION_NUMBER);
    }

    /**
     * Get page ID
     *
     * @return int
     */
    public function getPageId()
    {
        return parent::getData(self::PAGE_ID);
    }

    /**
     * Get user ID
     *
     * @return int
     */
    public function getUserId()
    {
        return parent::getData(self::USER_ID);
    }

    /**
     * Get page layout
     *
     * @return string
     */
    public function getPageLayout()
    {
        return parent::getData(self::PAGE_LAYOUT);
    }

    /**
     * Get meta keywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return parent::getData(self::META_KEYWORDS);
    }

    /**
     * Get meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return parent::getData(self::META_DESCRIPTION);
    }

    /**
     * Get content heading
     *
     * @return string
     */
    public function getContentHeading()
    {
        return parent::getData(self::CONTENT_HEADING);
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return parent::getData(self::CONTENT);
    }

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * Get layout update xml
     *
     * @return string
     */
    public function getLayoutUpdateXml()
    {
        return parent::getData(self::LAYOUT_UPDATE_XML);
    }

    /**
     * Get custom theme
     *
     * @return string
     */
    public function getCustomTheme()
    {
        return parent::getData(self::CUSTOM_THEME);
    }

    /**
     * Get custom page layout
     *
     * @return string
     */
    public function getCustomPageLayout()
    {
        return parent::getData(self::CUSTOM_PAGE_LAYOUT);
    }

    /**
     * Get custom layout update xml
     *
     * @return string
     */
    public function getCustomLayoutUpdateXml()
    {
        return parent::getData(self::CUSTOM_LAYOUT_UPDATE_XML);
    }

    /**
     * Get custom theme from
     *
     * @return string
     */
    public function getCustomThemeFrom()
    {
        return parent::getData(self::CUSTOM_THEME_FROM);
    }

    /**
     * Get custom theme to
     *
     * @return string
     */
    public function getCustomThemeTo()
    {
        return parent::getData(self::CUSTOM_THEME_TO);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setId($id)
    {
        return parent::setData(self::REVISION_ID, $id);
    }

    /**
     * Set version ID
     *
     * @param int $versionId
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setVersionId($versionId)
    {
        return parent::setData(self::VERSION_ID, $versionId);
    }

    /**
     * Set revision number
     *
     * @param int $revisionNumber
     * @return PageRevisionInterface
     */
    public function setRevisionNumber($revisionNumber)
    {
        return parent::setData(self::REVISION_NUMBER, $revisionNumber);
    }

    /**
     * Set page ID
     *
     * @param int $pageId
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setPageId($pageId)
    {
        return parent::setData(self::PAGE_ID, $pageId);
    }

    /**
     * Set user ID
     *
     * @param int $userId
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setUserId($userId)
    {
        return parent::setData(self::USER_ID, $userId);
    }

    /**
     * Set page layout
     *
     * @param string $pageLayout
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setPageLayout($pageLayout)
    {
        return parent::setData(self::PAGE_LAYOUT, $pageLayout);
    }

    /**
     * Set meta keywords
     *
     * @param string $metaKeywords
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setMetaKeywords($metaKeywords)
    {
        return parent::setData(self::META_KEYWORDS, $metaKeywords);
    }

    /**
     * Set meta description
     *
     * @param string $metaDescription
     * @return PageRevisionInterface
     */
    public function setMetaDescription($metaDescription)
    {
        return parent::setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * Set content heading
     *
     * @param string $contentHeading
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setContentHeading($contentHeading)
    {
        return parent::setData(self::CONTENT_HEADING, $contentHeading);
    }

    /**
     * Set content
     *
     * @param string $content
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setContent($content)
    {
        return parent::setData(self::CONTENT, $content);
    }

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setCreatedAt($createdAt)
    {
        return parent::setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Set layout update xml
     *
     * @param string $layoutUpdateXml
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setLayoutUpdateXml($layoutUpdateXml)
    {
        return parent::setData(self::LAYOUT_UPDATE_XML, $layoutUpdateXml);
    }

    /**
     * Set custom theme
     *
     * @param string $customTheme
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setCustomTheme($customTheme)
    {
        return parent::setData(self::CUSTOM_THEME, $customTheme);
    }

    /**
     * Set custom page layout
     *
     * @param string $customPageLayout
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setCustomPageLayout($customPageLayout)
    {
        return parent::setData(self::CUSTOM_PAGE_LAYOUT, $customPageLayout);
    }

    /**
     * Set custom layout update xml
     *
     * @param string $customLayoutUpdateXml
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setCustomLayoutUpdateXml($customLayoutUpdateXml)
    {
        return parent::setData(self::CUSTOM_LAYOUT_UPDATE_XML, $customLayoutUpdateXml);
    }

    /**
     * Set custom theme from
     *
     * @param string $customThemeFrom
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function setCustomThemeFrom($customThemeFrom)
    {
        return parent::setData(self::CUSTOM_THEME_FROM, $customThemeFrom);
    }

    /**
     * Set custom theme to
     *
     * @param string $customThemeTo
     * @return PageRevisionInterface
     */
    public function setCustomThemeTo($customThemeTo)
    {
        return parent::setData(self::CUSTOM_THEME_TO, $customThemeTo);
    }
}
