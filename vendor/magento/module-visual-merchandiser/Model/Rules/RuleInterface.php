<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VisualMerchandiser\Model\Rules;

interface RuleInterface
{
    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return void
     */
    public function applyToCollection($collection);

    /**
     * @return \Magento\VisualMerchandiser\Model\Rules\RuleInterface
     */
    public function get();

    /**
     * @return array
     */
    public function getNotices();

    /**
     * @return bool
     */
    public function hasNotices();
}
