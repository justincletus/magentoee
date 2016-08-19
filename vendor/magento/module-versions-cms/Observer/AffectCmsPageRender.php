<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VersionsCms\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AffectCmsPageRender implements ObserverInterface
{
    /**
     * Cms hierarchy
     *
     * @var \Magento\VersionsCms\Helper\Hierarchy
     */
    protected $cmsHierarchy;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    protected $view;

    /**
     * @param \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\ViewInterface $view
     */
    public function __construct(
        \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\ViewInterface $view
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->cmsHierarchy = $cmsHierarchy;
        $this->view = $view;
    }

    /**
     * Add Hierarchy Menu layout handle to Cms page rendering
     *
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(EventObserver $observer)
    {
        if (!is_object($this->coreRegistry->registry('current_cms_hierarchy_node'))
            || !$this->cmsHierarchy->isEnabled()) {
            return $this;
        }

        /* @var $node \Magento\VersionsCms\Model\Hierarchy\Node */
        $node = $this->coreRegistry->registry('current_cms_hierarchy_node');
        // collect loaded handles for cms page
        $loadedHandles = $this->view->getLayout()->getUpdate()->getHandles();

        $page = $observer->getPage();
        if ($page instanceof \Magento\CMS\Model\Page) {
            $loadedHandles[] = $page->getPageLayout();
        }

        $menuLayout = $node->getMenuLayout();
        if ($menuLayout === null) {
            return $this;
        }

        // check whether menu handle is compatible with page handles
        $allowedHandles = $menuLayout['pageLayoutHandles'];
        if (is_array($allowedHandles) && count($allowedHandles) > 0) {
            if (count(array_intersect($allowedHandles, $loadedHandles)) == 0) {
                return $this;
            }
        }

        // add menu handle to layout update
        $this->view->getLayout()->getUpdate()->addHandle($menuLayout['handle']);

        return $this;
    }
}
