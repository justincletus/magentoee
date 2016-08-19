<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Index;

class ChooserGrid extends \Magento\CustomerSegment\Controller\Adminhtml\Index
{
    /**
     * Grid ajax action in chooser mode
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
