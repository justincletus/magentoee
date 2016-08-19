<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Model\Plugin;

class ConvertQuoteAddressToOrderAddress
{
    /**
     * @var \Magento\CustomerCustomAttributes\Helper\Data
     */
    private $customerData;

    /**
     * @param \Magento\CustomerCustomAttributes\Helper\Data $customerData
     */
    public function __construct(
        \Magento\CustomerCustomAttributes\Helper\Data $customerData
    ) {
        $this->customerData = $customerData;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Api\Data\AddressInterface $quoteAddress
     * @param array $data
     * @return \Magento\Sales\Api\Data\OrderAddressInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Address\ToOrderAddress $subject,
        \Closure $proceed,
        \Magento\Quote\Api\Data\AddressInterface $quoteAddress,
        $data = []
    ) {
        $orderAddress = $proceed($quoteAddress, $data);
        $attributes = $this->customerData->getCustomerAddressUserDefinedAttributeCodes();
        foreach ($attributes as $attribute) {
            $orderAddress->setData($attribute, $quoteAddress->getData($attribute));
        }
        return $orderAddress;
    }
}
