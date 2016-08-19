<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Eway\Model\Adminhtml\Source;

use Magento\Payment\Model\Source\Cctype as PaymentCctype;

/**
 * Class Cctype provides source for backend cctypes selector
 */
class Cctype extends PaymentCctype
{
    /**
     * {@inheritdoc}
     */
    public function getAllowedTypes()
    {
        return ['AE', 'VI', 'MC', 'JCB', 'DN'];
    }

    /**
     * Geting credit cards types
     *
     * @return array
     */
    public function getCcTypes()
    {
        return $this->_paymentConfig->getCcTypes();
    }
}
