<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Model;

/**
 * Enterprise banner catalog rule model
 *
 * @method \Magento\Banner\Model\ResourceModel\Catalogrule _getResource()
 * @method \Magento\Banner\Model\ResourceModel\Catalogrule getResource()
 * @method int getBannerId()
 * @method \Magento\Banner\Model\Catalogrule setBannerId(int $value)
 * @method int getRuleId()
 * @method \Magento\Banner\Model\Catalogrule setRuleId(int $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Catalogrule extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize promo catalog price rule model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Banner\Model\ResourceModel\Catalogrule');
    }
}
