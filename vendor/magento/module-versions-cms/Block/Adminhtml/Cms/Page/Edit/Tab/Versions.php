<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Edit\Tab;

/**
 * Cms page edit form revisions tab
 */
class Versions extends \Magento\Backend\Block\Widget\Grid\Extended implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Array of admin users in system
     *
     * @var array
     */
    protected $_usersHash = null;

    /**
     * Cms data
     *
     * @var \Magento\VersionsCms\Helper\Data
     */
    protected $_cmsData;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @var \Magento\VersionsCms\Model\ResourceModel\Page\Version\CollectionFactory
     */
    protected $_versionCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\VersionsCms\Helper\Data $cmsData
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param \Magento\VersionsCms\Model\ResourceModel\Page\Version\CollectionFactory $versionCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\VersionsCms\Helper\Data $cmsData,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        \Magento\VersionsCms\Model\ResourceModel\Page\Version\CollectionFactory $versionCollectionFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_cmsData = $cmsData;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_cmsConfig = $cmsConfig;
        $this->_versionCollectionFactory = $versionCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
        $this->setId('versions');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepares collection of versions
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $userId = $this->_backendAuthSession->getUser()->getId();

        /* var $collection \Magento\VersionsCms\Model\ResourceModel\Page\Revision\Collection */
        $collection = $this->_versionCollectionFactory->create()->addPageFilter(
            $this->getPage()
        )->addVisibilityFilter(
            $userId,
            $this->_cmsConfig->getAllowedAccessLevel()
        )->addUserColumn()->addUserNameColumn();

        if (!$this->getParam($this->getVarNameSort())) {
            $collection->addNumberSort();
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Retrieve collection for grid if there is not collection call _prepareCollection
     *
     * @return \Magento\VersionsCms\Model\ResourceModel\Page\Version\Collection
     */
    public function getCollection()
    {
        if (!$this->_collection) {
            $this->_prepareCollection();
        }

        return $this->_collection;
    }

    /**
     * Prepare versions grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'label',
            [
                'header' => __('Version Label'),
                'index' => 'label',
                'type' => 'options',
                'options' => $this->getCollection()->getAsArray('label', 'label')
            ]
        );

        $this->addColumn(
            'owner',
            [
                'header' => __('Owner'),
                'index' => 'username',
                'type' => 'options',
                'options' => $this->getCollection()->getUsersArray(false),
                'width' => 250
            ]
        );

        $this->addColumn(
            'access_level',
            [
                'header' => __('Access Level'),
                'index' => 'access_level',
                'type' => 'options',
                'width' => 100,
                'options' => $this->_cmsData->getVersionAccessLevels()
            ]
        );

        $this->addColumn(
            'revisions',
            ['header' => __('Quantity'), 'index' => 'revisions_count', 'type' => 'number']
        );

        $this->addColumn(
            'created_at',
            ['width' => 150, 'header' => __('Created'), 'index' => 'created_at', 'type' => 'datetime']
        );

        return parent::_prepareColumns();
    }

    /**
     * Prepare url for reload grid through ajax
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/*/versions', ['_current' => true]);
    }

    /**
     * Returns cms page object from registry
     *
     * @return \Magento\Cms\Model\Page
     */
    public function getPage()
    {
        return $this->_coreRegistry->registry('cms_page');
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Versions');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Versions');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare massactions for this grid.
     * For now it is only ability to remove versions
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        if ($this->_cmsConfig->canCurrentUserDeleteVersion()) {
            $this->setMassactionIdField('version_id');
            $this->getMassactionBlock()->setFormFieldName('version');

            $this->getMassactionBlock()->addItem(
                'delete',
                [
                    'label' => __('Delete'),
                    'url' => $this->getUrl('adminhtml/*/massDeleteVersions', ['_current' => true]),
                    'confirm' => __('Are you sure?'),
                    'selected' => true
                ]
            );
        }
        return $this;
    }

    /**
     * Grid row event edit url
     *
     * @param object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'adminhtml/cms_page_version/edit',
            ['page_id' => $row->getPageId(), 'version_id' => $row->getVersionId()]
        );
    }
}
