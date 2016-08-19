<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Api\Data;

/**
 * Interface for cms version search results.
 */
interface PageVersionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get versions list.
     *
     * @return \Magento\VersionsCms\Api\Data\PageVersionInterface[]
     */
    public function getItems();

    /**
     * Set versions list.
     *
     * @param \Magento\VersionsCms\Api\Data\PageVersionInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
