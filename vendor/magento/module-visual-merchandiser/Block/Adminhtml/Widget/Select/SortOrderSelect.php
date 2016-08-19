<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VisualMerchandiser\Block\Adminhtml\Widget\Select;

use \Magento\VisualMerchandiser\Block\Adminhtml\Widget\Select;
use \Magento\VisualMerchandiser\Model\Sorting;

class SortOrderSelect extends Select
{
    /**
     * Get Select option values
     *
     * @return array
     */
    public function getSelectOptions()
    {
        return $this->_sorting->getSortingOptions();
    }
}
