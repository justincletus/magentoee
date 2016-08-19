<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesArchive\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SalesUpdateGridRecordsObserver implements ObserverInterface
{
    /**
     * @var \Magento\SalesArchive\Model\ArchiveFactory
     */
    protected $_archiveFactory;

    /**
     * @var \Magento\SalesArchive\Model\ArchivalList
     */
    protected $_archivalList;

    /**
     * @var \Magento\SalesArchive\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\SalesArchive\Model\ArchiveFactory $archiveFactory
     * @param \Magento\SalesArchive\Model\ArchivalList $archivalList
     * @param \Magento\SalesArchive\Model\Config $config
     */
    public function __construct(
        \Magento\SalesArchive\Model\ArchiveFactory $archiveFactory,
        \Magento\SalesArchive\Model\ArchivalList $archivalList,
        \Magento\SalesArchive\Model\Config $config
    ) {
        $this->_archiveFactory = $archiveFactory;
        $this->_archivalList = $archivalList;
        $this->_config = $config;
    }

    /**
     * Observes grid records update and depends on data updates records in grid too
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->_config->isArchiveActive()) {
            return $this;
        }

        $proxy = $observer->getEvent()->getProxy();

        $archive = $this->_archiveFactory->create();
        $archiveEntity = $this->_archivalList->getEntityByObject($proxy->getResource());

        if (!$archiveEntity) {
            return $this;
        }

        $ids = $proxy->getIds();
        $idsInArchive = $archive->getIdsInArchive($archiveEntity, $ids);
        // Exclude archive records from default grid rows update
        $ids = array_diff($ids, $idsInArchive);
        // Check for newly created shipments, creditmemos, invoices
        if ($archiveEntity != \Magento\SalesArchive\Model\ArchivalList::ORDER && !empty($ids)) {
            $relatedIds = $archive->getRelatedIds($archiveEntity, $ids);
            $ids = array_diff($ids, $relatedIds);
            $idsInArchive = array_merge($idsInArchive, $relatedIds);
        }

        $proxy->setIds($ids);

        if (!empty($idsInArchive)) {
            $archive->updateGridRecords($archiveEntity, $idsInArchive);
        }

        return $this;
    }
}
