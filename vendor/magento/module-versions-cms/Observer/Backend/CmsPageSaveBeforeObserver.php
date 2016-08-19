<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Observer\Backend;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class CmsPageSaveBeforeObserver implements ObserverInterface
{
    /**
     * Configuration model
     *
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $config;

    /**
     * Json Helper
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\VersionsCms\Model\Config $config
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\VersionsCms\Model\Config $config
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->config = $config;
    }

    /**
     * Prepare cms page object before it will be saved
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Cms\Model\Page $page */
        $page = $observer->getEvent()->getObject();

        /**
         * All new pages created by user without permission to publish
         * should be disabled from the beginning.
         */
        if (!$page->getId()) {
            $page->setIsNewPage(true);
            if (!$this->config->canCurrentUserPublishRevision()) {
                $page->setIsActive(false);
            }
            // newly created page should be auto assigned to website root
            $page->setWebsiteRoot(true);
        } elseif (!$page->getUnderVersionControl()) {
            $page->setPublishedRevisionId(null);
        }

        $nodesData = $this->getNodesOrder($page->getNodesData());

        $page->setNodesSortOrder($nodesData['sortOrder']);
        $page->setAppendToNodes($nodesData['appendToNodes']);
        return $this;
    }

    /**
     * Check nodes data and return new sort order for nodes
     *
     * @param string $nodesData
     * @return array
     */
    protected function getNodesOrder($nodesData)
    {
        $appendToNodes = [];
        $sortOrder = [];
        if ($nodesData) {
            try {
                $nodesData = $this->jsonHelper->jsonDecode($nodesData);
            } catch (\Zend_Json_Exception $e) {
                $nodesData = null;
            }
            if (!empty($nodesData)) {
                foreach ($nodesData as $row) {
                    if (isset($row['page_exists']) && $row['page_exists']) {
                        $appendToNodes[$row['node_id']] = 0;
                    }

                    if (isset($appendToNodes[$row['parent_node_id']])) {
                        if (strpos($row['node_id'], '_') !== false) {
                            $appendToNodes[$row['parent_node_id']] = $row['sort_order'];
                        } else {
                            $sortOrder[$row['node_id']] = $row['sort_order'];
                        }
                    }
                }
            }
        }

        return [
            'appendToNodes' => $appendToNodes,
            'sortOrder' => $sortOrder
        ];
    }
}
