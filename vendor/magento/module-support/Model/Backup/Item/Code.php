<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Model\Backup\Item;

use Magento\Support\Model\Backup\AbstractItem;

/**
 * Backup code
 */
class Code extends AbstractItem
{
    /**
     * {@inheritdoc}
     */
    protected function setCmdScriptName()
    {
        $this->cmdObject->setScriptName('bin/magento support:backup:code');
    }
}
