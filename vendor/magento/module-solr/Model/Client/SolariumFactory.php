<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\Model\Client;

class SolariumFactory implements \Magento\Solr\Model\Client\FactoryInterface
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        return $this->_objectManager->create('Magento\Solr\Model\Client\Solarium', ['options' => $options]);
    }
}
