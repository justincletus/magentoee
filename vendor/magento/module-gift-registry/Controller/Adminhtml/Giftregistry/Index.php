<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistry\Controller\Adminhtml\Giftregistry;

/**
 * @codeCoverageIgnore
 */
class Index extends \Magento\GiftRegistry\Controller\Adminhtml\Giftregistry
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
