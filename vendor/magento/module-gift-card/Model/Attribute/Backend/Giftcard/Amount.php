<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCard\Model\Attribute\Backend\Giftcard;

class Amount extends \Magento\Catalog\Model\Product\Attribute\Backend\Price
{
    /**
     * Giftcard amount backend resource model
     *
     * @var \Magento\GiftCard\Model\ResourceModel\Attribute\Backend\Giftcard\Amount
     */
    protected $_amountResource;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Directory helper
     *
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryHelper;

    /**
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\GiftCard\Model\ResourceModel\Attribute\Backend\Giftcard\Amount $amountResource
     */
    public function __construct(
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\GiftCard\Model\ResourceModel\Attribute\Backend\Giftcard\Amount $amountResource
    ) {
        $this->_storeManager = $storeManager;
        $this->_directoryHelper = $directoryHelper;
        $this->_amountResource = $amountResource;
        parent::__construct($currencyFactory, $storeManager, $catalogData, $config, $localeFormat);
    }

    /**
     * Validate data
     *
     * @param   \Magento\Catalog\Model\Product $object
     * @return  $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate($object)
    {
        $rows = $object->getData($this->getAttribute()->getName());
        if (empty($rows)) {
            return $this;
        }
        $dup = [];

        foreach ($rows as $row) {
            if (!isset($row['price']) || !empty($row['delete'])) {
                continue;
            }

            $key1 = implode('-', [$row['website_id'], $row['price']]);

            if (!empty($dup[$key1])) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Duplicate amount found.'));
            }
            $dup[$key1] = 1;
        }
        return $this;
    }

    /**
     * Assign amounts to product data
     *
     * @param   \Magento\Catalog\Model\Product $object
     * @return  $this
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function afterLoad($object)
    {
        $data = $this->_amountResource->loadProductData($object, $this->getAttribute());

        foreach ($data as $i => $row) {
            if ($data[$i]['website_id'] == 0) {
                $rate = $this->_storeManager->getStore()->getBaseCurrency()->getRate(
                    $this->_directoryHelper->getBaseCurrencyCode()
                );
                if ($rate) {
                    $data[$i]['website_value'] = $data[$i]['value'] / $rate;
                } else {
                    unset($data[$i]);
                }
            } else {
                $data[$i]['website_value'] = $data[$i]['value'];
            }
        }
        $object->setData($this->getAttribute()->getName(), $data);
        return $this;
    }

    /**
     * Save amounts data
     *
     * @param \Magento\Catalog\Model\Product $object
     * @return $this
     */
    public function afterSave($object)
    {
        $orig = $object->getOrigData($this->getAttribute()->getName());
        $current = $object->getData($this->getAttribute()->getName());
        if ($orig == $current) {
            return $this;
        }

        $this->_amountResource->deleteProductData($object, $this->getAttribute());
        $rows = $object->getData($this->getAttribute()->getName());

        if (!is_array($rows)) {
            return $this;
        }

        foreach ($rows as $row) {
            // Handle the case when model is saved whithout data received from user
            if ((!isset($row['price']) || empty($row['price'])) && !isset($row['value']) || !empty($row['delete'])) {
                continue;
            }

            $data = [];
            $data['website_id'] = $row['website_id'];
            $data['value'] = isset($row['price']) ? $row['price'] : $row['value'];
            $data['attribute_id'] = $this->getAttribute()->getId();

            $this->_amountResource->insertProductData($object, $data);
        }

        return $this;
    }

    /**
     * Delete amounts data
     *
     * @param \Magento\Catalog\Model\Product $object
     * @return $this
     */
    public function afterDelete($object)
    {
        $this->_amountResource->deleteProductData($object, $this->getAttribute());
        return $this;
    }

    /**
     * Retrieve storage table
     *
     * @return string
     */
    /*
        public function getTable()
        {
            return $this->_amountResource->getMainTable();
        }
    */
}
