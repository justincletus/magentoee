<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Api\Data;

/**
 * Interface for cms revision search results.
 */
interface PageRevisionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get revisions list.
     *
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface[]
     */
    public function getItems();

    /**
     * Set revisions list.
     *
     * @param \Magento\VersionsCms\Api\Data\PageRevisionInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
