<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class Index extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Default action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
