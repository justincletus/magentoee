<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VersionsCms\Model\Page;

use Magento\VersionsCms\Api\Data\PageRevisionInterface;

class RevisionManagement implements \Magento\VersionsCms\Api\PageRevisionManagementInterface
{
    /**
     * @var \Magento\VersionsCms\Api\PageRevisionRepositoryInterface
     */
    protected $revisionRepository;

    /**
     * @param \Magento\VersionsCms\Api\PageRevisionRepositoryInterface $revisionRepository
     */
    public function __construct(\Magento\VersionsCms\Api\PageRevisionRepositoryInterface $revisionRepository)
    {
        $this->revisionRepository = $revisionRepository;
    }

    /**
     * Publishing page revision by id
     *
     * @param int $pageRevisionId
     * @return \Magento\VersionsCms\Api\Data\PageRevisionInterface
     */
    public function publish($pageRevisionId)
    {
        /** @var $revision \Magento\VersionsCms\Api\Data\PageRevisionInterface */
        $revision = $this->revisionRepository->getById($pageRevisionId);
        return $revision->publish();
    }
}
