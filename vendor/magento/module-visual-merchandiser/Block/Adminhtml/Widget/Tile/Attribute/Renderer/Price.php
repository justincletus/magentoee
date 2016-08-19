<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VisualMerchandiser\Block\Adminhtml\Widget\Tile\Attribute\Renderer;

class Price extends \Magento\VisualMerchandiser\Block\Adminhtml\Widget\Tile\Attribute\Renderer
{
    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return '<span>' . $this->escaper->escapeHtml($this->getLabel() . ': ') . $this->getValue() . '</span></br>';
    }
}
