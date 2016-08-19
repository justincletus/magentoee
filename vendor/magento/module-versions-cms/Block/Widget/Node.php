<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Block\Widget;

/**
 * Cms Hierarchy Node Widget Block
 */
class Node extends \Magento\Framework\View\Element\Html\Link implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Current Hierarchy Node Page Instance
     *
     * @var \Magento\VersionsCms\Model\Hierarchy\Node
     */
    protected $node;

    /**
     * Current Store Id
     *
     * @var int
     */
    protected $storeId;

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
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\VersionsCms\Model\Hierarchy\NodeFactory $hierarchyNodeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\VersionsCms\Model\Hierarchy\NodeFactory $hierarchyNodeFactory,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->hierarchyNodeFactory = $hierarchyNodeFactory;
        parent::__construct($context, $data);
    }

    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $template = $this->getInstanceData('template');
        if ($template) {
            $this->setTemplate($template);
        }
    }

    /**
     * Retrieve specified anchor text
     *
     * @return string
     */
    public function getLabel()
    {
        $value = $this->getInstanceData('anchor_text');
        return $value !== false ? $value : $this->node->getLabel();
    }

    /**
     * Retrieve link specified title
     *
     * @return string
     */
    public function getTitle()
    {
        $value = $this->getInstanceData('title');
        return $value !== false ? $value : $this->node->getLabel();
    }

    /**
     * Retrieve Node ID
     *
     * @return mixed
     */
    public function getNodeId()
    {
        return $this->getInstanceData('node_id');
    }

    /**
     * Retrieve Node URL
     *
     * @return string
     */
    public function getHref()
    {
        return $this->node->getUrl();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getNodeId()) {
            $this->node = $this->hierarchyNodeFactory->create()->load($this->getNodeId());
        } else {
            $this->node = $this->coreRegistry->registry('current_cms_hierarchy_node');
        }

        if (!$this->node) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Retrieve Store Id
     *
     * @return int
     */
    protected function getStoreId()
    {
        if (null === $this->storeId) {
            $this->storeId = $this->_storeManager->getStore()->getId();
        }
        return $this->storeId;
    }

    /**
     * Retrieve data from instance
     *
     * @param string $key
     * @return mixed
     */
    protected function getInstanceData($key)
    {
        $dataKeys = [
            $key . '_' . $this->getStoreId(),
            $key . '_' . \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            $key,
        ];
        foreach ($dataKeys as $value) {
            if ($this->getData($value) !== null) {
                return $this->getData($value);
            }
        }
        return false;
    }
}
