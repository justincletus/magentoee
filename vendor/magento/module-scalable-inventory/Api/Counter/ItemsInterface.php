<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ScalableInventory\Api\Counter;

/**
 * Interface ItemsInterface
 */
interface ItemsInterface
{
    /**
     * @param \Magento\ScalableInventory\Api\Counter\ItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * @return \Magento\ScalableInventory\Api\Counter\ItemInterface[]
     */
    public function getItems();

    /**
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId);

    /**
     * @return int
     */
    public function getWebsiteId();

    /**
     * @param string $operator
     * @return $this
     */
    public function setOperator($operator);

    /**
     * @return string
     */
    public function getOperator();
}
