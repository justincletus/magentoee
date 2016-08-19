<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Controller\Adminhtml\Banner;

class Grid extends \Magento\Banner\Controller\Adminhtml\Banner
{
    /**
     * Render Banner grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
