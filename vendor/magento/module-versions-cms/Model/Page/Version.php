<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Model\Page;

use Magento\Framework\Model\AbstractModel;
use Magento\VersionsCms\Api\Data\PageVersionInterface;

/**
 * Cms page version model
 *
 * @method \Magento\VersionsCms\Model\ResourceModel\Page\Version _getResource()
 * @method \Magento\VersionsCms\Model\ResourceModel\Page\Version getResource()
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Version extends AbstractModel implements \Magento\VersionsCms\Api\Data\PageVersionInterface
{
    /**
     * Access level constants
     */
    const ACCESS_LEVEL_PRIVATE = 'private';

    const ACCESS_LEVEL_PROTECTED = 'protected';

    const ACCESS_LEVEL_PUBLIC = 'public';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'magento_versionscms_version';

    /**
     * Parameter name in event.
     * In observe method you can use $observer->getEvent()->getObject() in this case.
     *
     * @var string
     */
    protected $_eventObject = 'version';

    /**
     * @var \Magento\VersionsCms\Model\IncrementFactory
     */
    protected $_cmsIncrementFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_coreDate;

    /**
     * @var \Magento\VersionsCms\Model\ResourceModel\Increment
     */
    protected $_cmsResourceIncrement;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @var \Magento\VersionsCms\Model\Page\RevisionFactory
     */
    protected $_pageRevisionFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\VersionsCms\Model\IncrementFactory $cmsIncrementFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     * @param \Magento\VersionsCms\Model\ResourceModel\Increment $cmsResourceIncrement
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param \Magento\VersionsCms\Model\Page\RevisionFactory $pageRevisionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\VersionsCms\Model\IncrementFactory $cmsIncrementFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Magento\VersionsCms\Model\ResourceModel\Increment $cmsResourceIncrement,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        \Magento\VersionsCms\Model\Page\RevisionFactory $pageRevisionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_cmsIncrementFactory = $cmsIncrementFactory;
        $this->_coreDate = $coreDate;
        $this->_cmsResourceIncrement = $cmsResourceIncrement;
        $this->_cmsConfig = $cmsConfig;
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
        $this->_init('Magento\VersionsCms\Model\ResourceModel\Page\Version');
    }

    /**
     * Preparing data before save
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        if (!$this->getId()) {
            $incrementNumber = $this->_cmsIncrementFactory->create()->getNewIncrementId(
                \Magento\VersionsCms\Model\Increment::TYPE_PAGE,
                $this->getPageId(),
                \Magento\VersionsCms\Model\Increment::LEVEL_VERSION
            );

            $this->setVersionNumber($incrementNumber);
            $this->setCreatedAt($this->_coreDate->gmtDate());
        }

        if (!$this->getLabel()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a version label.'));
        }

        // We cannot allow changing access level for some versions
        if ($this->getAccessLevel() != $this->getOrigData('access_level')) {
            if ($this->getOrigData('access_level') == \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PUBLIC) {
                $resource = $this->_getResource();
                /* @var $resource \Magento\VersionsCms\Model\ResourceModel\Page\Version */

                if ($resource->isVersionLastPublic($this)) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Cannot change version access level because it is the last public version for its page.')
                    );
                }
            }
        }

        return parent::beforeSave();
    }

    /**
     * Processing some data after version saved
     *
     * @return $this
     */
    public function afterSave()
    {
        // If this was a new version we should create initial revision for it
        // from specified revision or from latest for parent version
        if ($this->getOrigData($this->getIdFieldName()) != $this->getId()) {
            $revision = $this->_pageRevisionFactory->create();

            // setting data for load
            $userId = $this->getUserId();
            $accessLevel = $this->_cmsConfig->getAllowedAccessLevel();

            if ($this->getInitialRevisionData()) {
                $revision->setData($this->getInitialRevisionData());
            } else {
                $revision->loadWithRestrictions(
                    $accessLevel,
                    $userId,
                    $this->getOrigData($this->getIdFieldName()),
                    'version_id'
                );
            }

            $revision->setVersionId($this->getId())->setUserId($userId)->setPageId($this->getPageId())->save();
            $this->setLastRevision($revision);
        }
        return parent::afterSave();
    }

    /**
     * Checking some moments before we can actually delete version
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDelete()
    {
        $resource = $this->_getResource();
        /* @var $resource \Magento\VersionsCms\Model\ResourceModel\Page\Version */
        if ($this->isPublic()) {
            if ($resource->isVersionLastPublic($this)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Version "%1" cannot be removed because it is the last public page version.', $this->getLabel())
                );
            }
        }

        if ($resource->isVersionHasPublishedRevision($this)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Version "%1" cannot be removed because its revision is published.', $this->getLabel())
            );
        }

        return parent::beforeDelete();
    }

    /**
     * Removing unneeded data from increment table after version was removed.
     *
     * @return $this
     */
    public function afterDelete()
    {
        $this->_cmsResourceIncrement->cleanIncrementRecord(
            \Magento\VersionsCms\Model\Increment::TYPE_PAGE,
            $this->getId(),
            \Magento\VersionsCms\Model\Increment::LEVEL_REVISION
        );

        return parent::afterDelete();
    }

    /**
     * Check if this version public or not.
     *
     * @return bool
     */
    public function isPublic()
    {
        return $this->getAccessLevel() == \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PUBLIC;
    }

    /**
     * Loading version with extra access level checking.
     *
     * @param array|string $accessLevel
     * @param int $userId
     * @param int|string $value
     * @param string|null $field
     * @return $this
     */
    public function loadWithRestrictions($accessLevel, $userId, $value, $field = null)
    {
        $this->_getResource()->loadWithRestrictions($this, $accessLevel, $userId, $value, $field = null);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Get ID
     *
     * @return string
     */
    public function getId()
    {
        return parent::getData(self::VERSION_ID);
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return parent::getData(self::LABEL);
    }

    /**
     * Get access level
     *
     * @return string
     */
    public function getAccessLevel()
    {
        return parent::getData(self::ACCESS_LEVEL);
    }

    /**
     * Get revisions count
     *
     * @return string
     */
    public function getRevisionsCount()
    {
        return parent::getData(self::REVISIONS_COUNT);
    }

    /**
     * Get version number
     *
     * @return string
     */
    public function getVersionNumber()
    {
        return parent::getData(self::VERSION_NUMBER);
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
     * Get page ID
     *
     * @return string
     */
    public function getPageId()
    {
        return parent::getData(self::PAGE_ID);
    }

    /**
     * Get user ID
     *
     * @return string
     */
    public function getUserId()
    {
        return parent::getData(self::USER_ID);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     */
    public function setId($id)
    {
        return parent::setData(self::VERSION_ID, $id);
    }

    /**
     * Set label
     *
     * @param string $label
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     */
    public function setLabel($label)
    {
        return parent::setData(self::LABEL, $label);
    }

    /**
     * Set access level
     *
     * @param string $accessLevel
     * @return PageVersionInterface
     */
    public function setAccessLevel($accessLevel)
    {
        return parent::setData(self::ACCESS_LEVEL, $accessLevel);
    }

    /**
     * Set revisions count
     *
     * @param int $revisionsCount
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     */
    public function setRevisionsCount($revisionsCount)
    {
        return parent::setData(self::REVISIONS_COUNT, $revisionsCount);
    }

    /**
     * Set version number
     *
     * @param int $versionNumber
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     */
    public function setVersionNumber($versionNumber)
    {
        return parent::setData(self::VERSION_NUMBER, $versionNumber);
    }

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     */
    public function setCreatedAt($createdAt)
    {
        return parent::setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Set page ID
     *
     * @param int $pageId
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     */
    public function setPageId($pageId)
    {
        return parent::setData(self::PAGE_ID, $pageId);
    }

    /**
     * Set user ID
     *
     * @param int $userId
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface
     */
    public function setUserId($userId)
    {
        return parent::setData(self::USER_ID, $userId);
    }
}
