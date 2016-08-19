<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\Model\Indexer\Fulltext\Plugin;

use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Plugin\AbstractPlugin;
use Magento\Solr\Helper\Data;

class CustomerGroup extends AbstractPlugin
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     * @param Data $helper
     */
    public function __construct(
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        Data $helper
    ) {
        parent::__construct($indexerRegistry);
        $this->helper = $helper;
    }

    /**
     * Invalidate indexer on customer group save
     *
     * @param \Magento\Customer\Model\ResourceModel\Group $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Model\AbstractModel $group
     *
     * @return \Magento\Catalog\Model\ResourceModel\Attribute
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Customer\Model\ResourceModel\Group $subject,
        \Closure $proceed,
        \Magento\Framework\Model\AbstractModel $group
    ) {
        $needInvalidation = $this->helper->isThirdPartyEngineAvailable()
            && ($group->isObjectNew() || $group->dataHasChangedFor('tax_class_id'));
        $result = $proceed($group);
        if ($needInvalidation) {
            $this->indexerRegistry->get(Fulltext::INDEXER_ID)->invalidate();
        }
        return $result;
    }
}
