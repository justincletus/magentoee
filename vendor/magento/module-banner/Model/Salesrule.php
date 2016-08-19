<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Model;

/**
 * Enterprise banner sales rule model
 *
 * @method \Magento\Banner\Model\ResourceModel\Salesrule _getResource()
 * @method \Magento\Banner\Model\ResourceModel\Salesrule getResource()
 * @method int getBannerId()
 * @method \Magento\Banner\Model\Salesrule setBannerId(int $value)
 * @method int getRuleId()
 * @method \Magento\Banner\Model\Salesrule setRuleId(int $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Salesrule extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize promo cart price rule model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Banner\Model\ResourceModel\Salesrule');
    }
}
