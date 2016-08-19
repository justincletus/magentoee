<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Api;

/**
 * Interface CommentRepositoryInterface
 * @api
 */
interface CommentRepositoryInterface
{
    /**
     * Get comment by id
     *
     * @param int $id
     * @return \Magento\Rma\Api\Data\CommentSearchResultInterface
     */
    public function get($id);

    /**
     * Get comments list
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Rma\Api\Data\CommentSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria);

    /**
     * Save comment
     *
     * @param \Magento\Rma\Api\Data\CommentInterface $comment
     * @return bool
     */
    public function save(\Magento\Rma\Api\Data\CommentInterface $comment);

    /**
     * Delete comment
     *
     * @param \Magento\Rma\Api\Data\CommentInterface $comment
     * @return bool
     */
    public function delete(\Magento\Rma\Api\Data\CommentInterface $comment);
}
