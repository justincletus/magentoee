<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Observer\Backend;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\VersionsCms\Model\Page\Version;
use Magento\VersionsCms\Model\Page\VersionFactory;
use Magento\VersionsCms\Model\ResourceModel\Page\Version\CollectionFactory;

class AdminUserDeleteAfterObserver implements ObserverInterface
{
    /**
     * @var VersionFactory
     */
    protected $pageVersionFactory;

    /**
     * @var CollectionFactory
     */
    protected $versionCollectionFactory;

    /**
     * @var Iterator
     */
    protected $resourceIterator;

    /**
     * @var RemoveVersionCallback
     */
    protected $removeVersionCallback;

    /**
     * @param VersionFactory $pageVersionFactory
     * @param CollectionFactory $versionCollectionFactory
     * @param Iterator $resourceIterator
     * @param RemoveVersionCallback $removeVersionCallback
     */
    public function __construct(
        VersionFactory $pageVersionFactory,
        CollectionFactory $versionCollectionFactory,
        Iterator $resourceIterator,
        RemoveVersionCallback $removeVersionCallback
    ) {
        $this->pageVersionFactory = $pageVersionFactory;
        $this->versionCollectionFactory = $versionCollectionFactory;
        $this->resourceIterator = $resourceIterator;
        $this->removeVersionCallback = $removeVersionCallback;
    }

    /**
     * Clean up private versions after user deleted.
     *
     * @param mixed $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\VersionsCms\Model\ResourceModel\Page\Version\Collection $collection */
        $collection = $this->versionCollectionFactory->create()
            ->addAccessLevelFilter(Version::ACCESS_LEVEL_PRIVATE)
            ->addUserIdFilter();

        $this->resourceIterator->walk(
            $collection->getSelect(),
            [[$this->removeVersionCallback, 'execute']],
            ['version' => $this->pageVersionFactory->create()]
        );
    }
}
