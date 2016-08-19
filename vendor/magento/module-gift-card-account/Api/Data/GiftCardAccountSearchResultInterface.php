<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardAccount\Api\Data;

/**
 * Interface GiftCardAccountSearchResultInterface
 */
interface GiftCardAccountSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get GiftCard Account list
     *
     * @return \Magento\GiftCardAccount\Api\Data\GiftCardAccountInterface[]
     */
    public function getItems();
}
