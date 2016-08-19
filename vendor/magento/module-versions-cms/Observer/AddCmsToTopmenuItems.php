<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Versions cms page observer
 */
namespace Magento\VersionsCms\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddCmsToTopmenuItems implements ObserverInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\NodeFactory
     */
    protected $hierarchyNodeFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\VersionsCms\Model\Hierarchy\NodeFactory $hierarchyNodeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\VersionsCms\Model\Hierarchy\NodeFactory $hierarchyNodeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->hierarchyNodeFactory = $hierarchyNodeFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Adds CMS hierarchy menu item to top menu
     *
     * @param EventObserver $observer
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute(EventObserver $observer)
    {
        /**
         * @var $topMenuRootNode \Magento\Framework\Data\Tree\Node
         */
        $topMenuRootNode = $observer->getMenu();

        $hierarchyModel = $this->hierarchyNodeFactory->create(
            [
                'data' => [
                    'scope' => \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_STORE,
                    'scope_id' => $this->storeManager->getStore()->getId(),
                ],
            ]
        )->getHeritage();

        $nodes = $hierarchyModel->getNodesData();
        $tree = $topMenuRootNode->getTree();

        $nodesFlatList = [$topMenuRootNode->getId() => $topMenuRootNode];

        $nodeModel = $this->hierarchyNodeFactory->create();

        foreach ($nodes as $node) {
            $nodeData = $nodeModel->load($node['node_id']);

            if (!$nodeData ||
                $nodeData->getParentNodeId() == null && !$nodeData->getTopMenuVisibility() ||
                $nodeData->getParentNodeId() != null && $nodeData->getTopMenuExcluded() ||
                $nodeData->getPageId() && !$nodeData->getPageIsActive()
            ) {
                continue;
            }

            $menuNodeId = 'cms-hierarchy-node-' . $node['node_id'];
            $menuNodeData = [
                'name' => $nodeData->getLabel(),
                'id' => $menuNodeId,
                'url' => $nodeData->getUrl(),
                'is_active' => $this->_isCmsNodeActive($nodeData),
            ];

            $parentNodeId = !isset(
                $node['parent_node_id']
            ) ? $topMenuRootNode->getId() : 'cms-hierarchy-node-' . $node['parent_node_id'];
            $parentNode = isset($nodesFlatList[$parentNodeId]) ? $nodesFlatList[$parentNodeId] : null;

            if (!$parentNode) {
                continue;
            }

            $menuNode = new \Magento\Framework\Data\Tree\Node($menuNodeData, 'id', $tree, $parentNode);
            $parentNode->addChild($menuNode);

            $nodesFlatList[$menuNodeId] = $menuNode;
        }
    }

    /**
     * Checks whether node belongs to currently active node's path
     *
     * @param \Magento\VersionsCms\Model\Hierarchy\Node $cmsNode
     * @return bool
     */
    protected function _isCmsNodeActive($cmsNode)
    {
        $currentNode = $this->coreRegistry->registry('current_cms_hierarchy_node');

        if (!$currentNode) {
            return false;
        }

        $nodePathIds = explode('/', $currentNode->getXpath());

        return in_array($cmsNode->getId(), $nodePathIds);
    }
}
