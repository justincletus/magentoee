<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\Model\Indexer;

use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Framework\Search\Request\Dimension;
use Magento\Framework\Indexer\SaveHandler\Batch;
use Magento\Solr\Model\Adapter\Solarium;
use Magento\Solr\Model\AdapterFactoryInterface;
use Magento\Solr\Model\Adminhtml\Source\IndexationMode;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Framework\App\Config\ScopeConfigInterface;

class IndexerHandler implements IndexerInterface
{
    /**
     * Config key for indexation mode configuration
     */
    const COMMIT_MODE_XML_PATH = 'catalog/search/engine_commit_mode';

    /**
     * Scope identifier
     */
    const SCOPE_FIELD_NAME = 'scope';

    /**
     * @var Solarium
     */
    private $adapter;

    /**
     * Scope config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var array
     */
    private $data;

    /**
     * @var Batch
     */
    private $batch;

    /**
     * @var int
     */
    private $batchSize;

    /**
     * @param AdapterFactoryInterface $adapterFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param Batch $batch
     * @param array $data
     * @param int $batchSize
     */
    public function __construct(
        AdapterFactoryInterface $adapterFactory,
        ScopeConfigInterface $scopeConfig,
        Batch $batch,
        array $data = [],
        $batchSize = 500
    ) {
        $this->adapter = $adapterFactory->createAdapter();
        $this->scopeConfig = $scopeConfig;
        $this->data = $data;
        $this->batch = $batch;
        $this->batchSize = $batchSize;
    }

    /**
     * Get commit mode
     * @param Dimension $dimension
     * @return string
     */
    private function getCommitMode(Dimension $dimension)
    {
        return $this->scopeConfig->getValue(
            self::COMMIT_MODE_XML_PATH,
            ScopeInterface::SCOPE_STORE,
            $dimension->getValue()
        );
    }

    /**
     * @param Dimension $dimension
     * @return void
     */
    private function commitChanges(Dimension $dimension)
    {
        if ($this->getCommitMode($dimension) == IndexationMode::MODE_FINAL) {
            $this->adapter->allowCommit();
            $this->adapter->commit();
        }
    }

    /**
     * @param Dimension $dimension
     * @return void
     */
    private function setCommitMode(Dimension $dimension)
    {
        if ($this->getCommitMode($dimension) != IndexationMode::MODE_PARTIAL) {
            $this->adapter->holdCommit();
        } else {
            $this->adapter->allowCommit();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function saveIndex($dimensions, \Traversable $documents)
    {
        $dimension = current($dimensions);
        $storeId = $this->getStoreIdByDimension($dimension);
        $this->setCommitMode($dimension);
        foreach ($this->batch->getItems($documents, $this->batchSize) as $documentsBatch) {
            $docs = $this->adapter->prepareDocsPerStore($documentsBatch, $storeId);
            $this->adapter->addDocs($docs);
        }
        $this->commitChanges($dimension);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteIndex($dimensions, \Traversable $documents)
    {
        $dimension = reset($dimensions);
        $storeId = $this->getStoreIdByDimension($dimension);
        $this->setCommitMode($dimension);

        $queries = [];
        $uniqueKey = $this->adapter->getUniqueKey();
        foreach ($documents as $entityId) {
            $queries[] = $uniqueKey . ':' . $entityId . '|' . $storeId;
        }

        $this->adapter->deleteDocs($queries);
        $this->commitChanges($dimension);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function cleanIndex($dimensions)
    {
        $this->adapter->deleteDocs(['store_id:' . $dimensions[0]->getValue()]);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isAvailable()
    {
        return $this->adapter->ping();
    }

    /**
     * @param Dimension $dimension
     * @return int
     */
    private function getStoreIdByDimension($dimension)
    {
        return $dimension->getName() == self::SCOPE_FIELD_NAME ? $dimension->getValue() : Store::DEFAULT_STORE_ID;
    }
}
