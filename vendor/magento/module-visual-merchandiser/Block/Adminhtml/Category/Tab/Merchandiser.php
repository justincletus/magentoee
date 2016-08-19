<?php
/***
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Block\Adminhtml\Category\Tab;

class Merchandiser extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\VisualMerchandiser\Model\Position\Cache
     */
    protected $_positionCache;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\VisualMerchandiser\Model\Position\Cache $cache
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\VisualMerchandiser\Model\Position\Cache $cache,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_positionCache = $cache;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getDialogUrl()
    {
        return $this->getUrl(
            'merchandiser/*/addproduct',
            [
                'cache_key' => $this->getPositionCacheKey(),
                'componentJson' => true
            ]
        );
    }

    /**
     * @return string
     */
    public function getSavePositionsUrl()
    {
        return $this->getUrl('merchandiser/position/save');
    }

    /**
     * Get products positions url
     *
     * @return string
     */
    public function getProductsPositionsUrl()
    {
        return $this->getUrl('merchandiser/position/get');
    }

    /**
     * @return mixed
     */
    public function getCategoryId()
    {
        return $this->getRequest()->getParam('id');
    }

    /**
     * @return string
     */
    public function getPositionCacheKey()
    {
        return $this->_coreRegistry->registry($this->getPositionCacheKeyName());
    }

    /**
     * @return string
     */
    public function getPositionCacheKeyName()
    {
        return \Magento\VisualMerchandiser\Model\Position\Cache::POSITION_CACHE_KEY;
    }

    /**
     * @return string
     */
    public function getPositionDataJson()
    {
        return \Zend_Json::encode($this->_positionCache->getPositions($this->getPositionCacheKey()));
    }
}
