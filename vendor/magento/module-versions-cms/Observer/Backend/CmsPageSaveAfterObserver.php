<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Observer\Backend;

use Magento\Backend\Model\Auth\Session;
use Magento\Cms\Model\Page;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\VersionsCms\Helper\Hierarchy;
use Magento\VersionsCms\Model\Hierarchy\Node as HierarchyNode;
use Magento\VersionsCms\Model\Page\Revision;
use Magento\VersionsCms\Model\Page\Version;
use Magento\VersionsCms\Model\Page\VersionFactory;
use Magento\VersionsCms\Model\ResourceModel\Hierarchy\Node;

/**
 * Class CmsPageSaveAfterObserver
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @package Magento\VersionsCms\Observer\Backend
 */
class CmsPageSaveAfterObserver implements ObserverInterface
{
    /**
     * Cms hierarchy
     *
     * @var Hierarchy
     */
    protected $cmsHierarchy;

    /**
     * @var HierarchyNode
     */
    protected $hierarchyNode;

    /**
     * @var Node
     */
    protected $hierarchyNodeResource;

    /**
     * @var VersionFactory
     */
    protected $pageVersionFactory;

    /**
     * @var Session
     */
    protected $backendAuthSession;

    /**
     * @param Hierarchy $cmsHierarchy
     * @param HierarchyNode $hierarchyNode
     * @param Node $hierarchyNodeResource
     * @param VersionFactory $pageVersionFactory
     * @param Session $backendAuthSession
     */
    public function __construct(
        Hierarchy $cmsHierarchy,
        HierarchyNode $hierarchyNode,
        Node $hierarchyNodeResource,
        VersionFactory $pageVersionFactory,
        Session $backendAuthSession
    ) {
        $this->cmsHierarchy = $cmsHierarchy;
        $this->hierarchyNode = $hierarchyNode;
        $this->hierarchyNodeResource = $hierarchyNodeResource;
        $this->pageVersionFactory = $pageVersionFactory;
        $this->backendAuthSession = $backendAuthSession;
    }

    /**
     * Process extra data after cms page saved
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        /** @var Page $page */
        $page = $observer->getEvent()->getObject();

        $this->createNewInitialVersionRevision($page);

        if (!$this->cmsHierarchy->isEnabled()) {
            return $this;
        }

        // rebuild URL rewrites if page has changed for identifier
        if ($page->dataHasChangedFor('identifier')) {
            $this->hierarchyNode->updateRewriteUrls($page);
        }

        /**
         * Append page to selected nodes it will remove pages from other nodes
         * which are not specified in array. So should be called even array is empty!
         * Returns array of new ids for page nodes array( oldId => newId ).
         */
        $this->hierarchyNode->appendPageToNodes($page, $page->getAppendToNodes());

        /**
         * Update sort order for nodes in parent nodes which have current page as child
         */
        foreach ($page->getNodesSortOrder() as $nodeId => $value) {
            $this->hierarchyNodeResource->updateSortOrder($nodeId, $value);
        }

        return $this;
    }

    /**
     * Create new initial version & revision
     *
     * If it is a new page or version control was turned on for this page.
     *
     * @param Page $page
     * @return void
     */
    protected function createNewInitialVersionRevision(Page $page)
    {
        if (
            $page->getIsNewPage()
            || ($page->getUnderVersionControl()
            && $page->dataHasChangedFor('under_version_control'))
        ) {
            /** @var Version $version */
            $version = $this->pageVersionFactory->create();

            $revisionInitialData = $page->getData();
            $revisionInitialData['copied_from_original'] = true;

            $version->setLabel($page->getTitle())
                ->setAccessLevel(Version::ACCESS_LEVEL_PUBLIC)
                ->setPageId($page->getId())
                ->setUserId($this->backendAuthSession->getUser()->getId())
                ->setInitialRevisionData($revisionInitialData)
                ->save();

            if ($page->getUnderVersionControl()) {
                $revision = $version->getLastRevision();

                if ($revision instanceof Revision) {
                    $revision->publish();
                }
            }
        }
    }
}
