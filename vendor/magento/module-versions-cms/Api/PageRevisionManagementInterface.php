<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Api;

/**
 * Interface for managing page revisions.
 */
interface PageRevisionManagementInterface
{
    /**
     * Publishing page revision by id
     *
     * @param int $pageRevisionId
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function publish($pageRevisionId);
}
