<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdminGws\Block\Adminhtml\Permissions\Grid;

/**
 * Admin roles grid
 *
 */
class Role extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * Add allowed websites/stores column
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn(
            'gws',
            [
                'header' => __('Allowed Scopes'),
                'width' => '200',
                'sortable' => false,
                'filter' => false,
                'renderer' => 'Magento\AdminGws\Block\Adminhtml\Permissions\Grid\Renderer\Gws'
            ]
        );

        return $this;
    }
}
