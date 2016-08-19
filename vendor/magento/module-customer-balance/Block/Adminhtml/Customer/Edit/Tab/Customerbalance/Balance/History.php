<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerBalance\Block\Adminhtml\Customer\Edit\Tab\Customerbalance\Balance;

class History extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'balance/history.phtml';

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                'Magento\CustomerBalance\Block\Adminhtml\Customer\Edit\Tab\Customerbalance\Balance\History\Grid',
                'customer.balance.history.grid'
            )
        );
        return parent::_prepareLayout();
    }
}
