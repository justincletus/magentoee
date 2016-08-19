<?php
/***
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Block\Adminhtml\Category\Tab\Merchandiser;

/**
 * @method string getPositionCacheKey()
 */
class Grid extends \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
{
    /**
     * @var \Magento\VisualMerchandiser\Model\Category\Products
     */
    protected $_products;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\VisualMerchandiser\Model\Category\Products $products
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\VisualMerchandiser\Model\Category\Products $products,
        array $data = []
    ) {
        $this->_products = $products;
        parent::__construct($context, $backendHelper, $products->getFactory(), $coreRegistry, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('position');
        $this->setDefaultDir('asc');
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'draggable-position',
            [
                'renderer' => 'Magento\Backend\Block\Widget\Grid\Column\Renderer\DraggableHandle',
                'index' => 'entity_id',
                'inline_css' => 'draggable-handle',
            ]
        );

        parent::_prepareColumns();

        $this->removeColumn('position');
        $this->removeColumn('in_category');

        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'type' => 'number',
                'index' => 'position',
                'renderer' => 'Magento\VisualMerchandiser\Block\Adminhtml\Widget\Grid\Column\Renderer\Position'
            ]
        );

        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'index' => 'entity_id',
                'renderer' => '\Magento\Backend\Block\Widget\Grid\Column\Renderer\Action',
                'filter' => false,
                'sortable' => false,
                'actions' => [
                    [
                        'caption' => __('Unassign'),
                        'url' => '#',
                        'name' => 'unassign'
                    ],
                ]
            ]
        );

        $this->getColumnSet()->setSortable(false);
        $this->setFilterVisibility(false);

        return $this;
    }

    /**
     * @return string
     */
    protected function _getPositionCacheKey()
    {
        return $this->getPositionCacheKey() ?
            $this->getPositionCacheKey() :
            $this->getParentBlock()->getPositionCacheKey();
    }

    /**
     * Prepare grid collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $this->_products->setCacheKey($this->_getPositionCacheKey());

        $collection = $this->_products->getCollectionForGrid(
            (int) $this->getRequest()->getParam('id', 0),
            $this->getRequest()->getParam('store')
        );

        $collection = $this->_products->applyCachedChanges($collection);

        if (!$collection) {
            return $this;
        }

        $collection->clear();
        $this->setCollection($collection);

        $this->_preparePage();

        $idx = ($collection->getCurPage() * $collection->getPageSize()) - $collection->getPageSize();

        foreach ($collection as $item) {
            $item->setPosition($idx);
            $idx++;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('merchandiser/*/grid', ['_current' => true]);
    }
}
