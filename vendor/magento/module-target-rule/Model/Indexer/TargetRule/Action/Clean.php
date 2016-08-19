<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule\Action;

/**
 * Class Clean index action
 *
 * @package Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action
 */
class Clean extends \Magento\TargetRule\Model\Indexer\TargetRule\AbstractAction
{
    /**
     * Execute clean index
     *
     * @param null|array $ids
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($ids = null)
    {
        try {
            $this->_cleanAll();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
    }
}
