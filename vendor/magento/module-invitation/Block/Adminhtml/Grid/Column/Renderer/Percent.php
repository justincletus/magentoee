<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Adminhtml grid percent column renderer
 *
 */
namespace Magento\Invitation\Block\Adminhtml\Grid\Column\Renderer;

class Percent extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Number
{
    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($this->getColumn()->getEditable()) {
            return parent::render($row);
        }

        $value = $this->_getValue($row);

        $value = round($value, 2);

        return $value . ' %';
    }
}
