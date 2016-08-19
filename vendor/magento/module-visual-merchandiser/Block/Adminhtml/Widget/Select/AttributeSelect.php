<?php
/***
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VisualMerchandiser\Block\Adminhtml\Widget\Select;

use \Magento\VisualMerchandiser\Block\Adminhtml\Widget\Select;

class AttributeSelect extends Select
{
    /**
     * Get Select option values
     *
     * @return array
     */
    public function getSelectOptions()
    {
        $result = [];
        $result[""] = __('Choose a selection...');
        return array_merge($result, $this->_rules->getAvailableAttributes());
    }
}
