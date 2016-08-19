<?php
/***
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Block\Adminhtml\Category\Tab\Merchandiser;

class Tile extends \Magento\Backend\Block\Widget\Grid
{
    const XML_PATH_ADDITIONAL_ATTRIBUTES = 'visualmerchandiser/options/product_attributes';
    const IMAGE_WIDTH = 130;
    const IMAGE_HEIGHT = 130;

    /**
     * @var string
     */
    protected $_template = 'category/tab/merchandiser/tile.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Collection object
     *
     * @var \Magento\Framework\Data\Collection
     */
    protected $_collection;

    /**
     * Catalog image
     *
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_catalogImage = null;

    /**
     * @var \Magento\VisualMerchandiser\Model\Category\Products
     */
    protected $_products;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var array
     */
    protected $usableAttributes = null;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $attributeFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Catalog\Helper\Image $catalogImage
     * @param \Magento\VisualMerchandiser\Model\Category\Products $products
     * @param \Magento\VisualMerchandiser\Block\Adminhtml\Widget\Tile\Attribute\Factory $attributeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Helper\Image $catalogImage,
        \Magento\VisualMerchandiser\Model\Category\Products $products,
        \Magento\VisualMerchandiser\Block\Adminhtml\Widget\Tile\Attribute\Factory $attributeFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_catalogImage = $catalogImage;
        $this->_products = $products;
        $this->scopeConfig = $context->getScopeConfig();
        $this->attributeFactory = $attributeFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('position');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    /**
     * @return \Magento\Catalog\Helper\Image
     */
    public function getImageHelper()
    {
        return $this->_catalogImage;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getImageUrl($product)
    {
        $image = $this->getImageHelper()
            ->init($product, 'small_image', ['type' => 'small_image'])
            ->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
        return $image->getUrl();
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    protected function _prepareGrid()
    {
        $this->_prepareCollection();
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
     * @return $this
     */
    protected function _prepareCollection()
    {
        $this->_products->setCacheKey($this->getPositionCacheKey());

        $collection = $this->_products->getCollectionForGrid(
            (int) $this->getRequest()->getParam('id', 0),
            $this->getRequest()->getParam('store')
        );

        $collection = $this->_products->applyCachedChanges($collection);

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
     * Set collection object
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return void
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
    }

    /**
     * get collection object
     *
     * @return \Magento\Framework\Data\Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * Retrieve column by id
     *
     * @param string $columnId
     * @return \Magento\Framework\View\Element\AbstractBlock|bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getColumn($columnId)
    {
        return false;
    }

    /**
     * Retrieve list of grid columns
     *
     * @return array
     */
    public function getColumns()
    {
        return [];
    }

    /**
     * Process column filtration values
     *
     * @param mixed $data
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _setFilterValues($data)
    {
        return $this;
    }

    /**
     * @return array|null
     */
    public function getCategory()
    {
        return $this->_coreRegistry->registry('category');
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('merchandiser/*/tile', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function getUsableAttributes()
    {
        if ($this->usableAttributes == null) {
            $attributeCodes = (string) $this->scopeConfig->getValue(self::XML_PATH_ADDITIONAL_ATTRIBUTES);
            $attributeCodes = explode(',', $attributeCodes);
            $this->usableAttributes = array_map('trim', $attributeCodes);
        }
        return $this->usableAttributes;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAttributesToDisplay($product)
    {
        $attributeCodes = $this->getUsableAttributes();
        $availableAttributes = $product->getTypeInstance()->getSetAttributes($product);
        $availableFields = array_keys($product->getData());
        $filteredAttributes = [];

        foreach ($attributeCodes as $code) {
            $renderer = $this->attributeFactory->create($code);

            if ($code == 'price') {
                $attributeObject = $availableAttributes[$code];
                $filteredAttributes[] = $renderer->addData([
                    'label' => $attributeObject->getFrontend()->getLabel(),
                    'value' => $product->getFormatedPrice()
                ]);
            } elseif (isset($availableAttributes[$code])) {
                $attributeObject = $availableAttributes[$code];
                $filteredAttributes[] = $renderer->addData([
                    'label' => $attributeObject->getFrontend()->getLabel(),
                    'value' => $product->getData($code)
                ]);
            } else if (in_array($code, $availableFields)) {
                $filteredAttributes[] = $renderer->addData([
                    'label' => ucwords($code),
                    'value' => $product->getData($code)
                ]);
            }
        }

        return $filteredAttributes;
    }
}
