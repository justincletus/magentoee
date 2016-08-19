<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog Event on category page
 */
namespace Magento\CatalogEvent\Block\Catalog\Product;

use \Magento\Framework\DataObject\IdentityInterface;
use \Magento\CatalogEvent\Block\Event\AbstractEvent;

class Event extends AbstractEvent implements IdentityInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Catalog event data
     *
     * @var \Magento\CatalogEvent\Helper\Data
     */
    protected $_catalogEventData;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\CatalogEvent\Helper\Data $catalogEventData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Registry $registry,
        \Magento\CatalogEvent\Helper\Data $catalogEventData,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_catalogEventData = $catalogEventData;
        parent::__construct($context, $localeResolver, $data);
    }

    /**
     * Return current category event
     *
     * @return \Magento\CatalogEvent\Block\Catalog\Category\Event
     */
    public function getEvent()
    {
        if ($this->getProduct()) {
            return $this->getProduct()->getEvent();
        }

        return false;
    }

    /**
     * Return current category
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Check availability to display event block
     *
     * @return boolean
     */
    public function canDisplay()
    {
        return $this->_catalogEventData->isEnabled()
            && $this->getProduct()
            && $this->getEvent()
            && $this->getEvent()->canDisplayProductPage()
            && !$this->getProduct()->getEventNoTicker();
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return $this->getProduct()->getIdentities();
    }
}
