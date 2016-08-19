<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PersistentHistory\Observer;

class QuotePersistentPreventFlag
{
    /**
     * Whether set quote to be persistent in workflow
     *
     * @var bool
     */
    protected $_quotePersistent = true;

    /**
     * @param bool $quotePersistent
     * @return void
     */
    public function setValue($quotePersistent)
    {
        $this->_quotePersistent = $quotePersistent;
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getValue()
    {
        return $this->_quotePersistent;
    }
}
