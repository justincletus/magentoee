<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\SearchAdapter;

/**
 * Document Factory
 */
class DocumentFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Search\EntityMetadata
     */
    private $entityMetadata;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Search\EntityMetadata $entityMetadata
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Search\EntityMetadata $entityMetadata
    ) {
        $this->objectManager = $objectManager;
        $this->entityMetadata = $entityMetadata;
    }

    /**
     * Create Search Document instance
     *
     * @param mixed $rawDocument
     * @return \Magento\Framework\Search\Document
     */
    public function create($rawDocument)
    {
        /** @var \Magento\Framework\Search\DocumentField[] $fields */
        $fields = [];
        $documentId = null;
        $entityId = $this->entityMetadata->getEntityId();
        foreach ($rawDocument->getFields() as $fieldName => $value) {
            if ($fieldName === $entityId) {
                $documentId = $value;
            } elseif ($fieldName === 'score') {
                $fields[$fieldName] = new \Magento\Framework\Search\DocumentField($fieldName, $value);
            }
        }
        return new \Magento\Framework\Search\Document($documentId, $fields);
    }
}
