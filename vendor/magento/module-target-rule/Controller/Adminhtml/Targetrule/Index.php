<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TargetRule\Controller\Adminhtml\Targetrule;

class Index extends \Magento\TargetRule\Controller\Adminhtml\Targetrule
{
    /**
     * Index grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Related Products Rules'));
        $this->_view->renderLayout();
    }
}
