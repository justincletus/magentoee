<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VisualMerchandiser\Block\Adminhtml\Widget\Select;

use \Magento\VisualMerchandiser\Block\Adminhtml\Widget\Select;
use \Magento\VisualMerchandiser\Model\Rules;

class LogicSelect extends Select
{
    /**
     * Get Select option values
     *
     * @return array
     */
    public function getSelectOptions()
    {
        $logic = Rules::getLogicVariants();
        return array_combine($logic, $logic);
    }
}
