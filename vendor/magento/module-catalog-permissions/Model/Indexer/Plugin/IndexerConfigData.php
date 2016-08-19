<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

class IndexerConfigData
{
    /**
     * @var \Magento\CatalogPermissions\App\Config
     */
    protected $config;

    /**
     * @param \Magento\CatalogPermissions\App\Config $config
     */
    public function __construct(\Magento\CatalogPermissions\App\Config $config)
    {
        $this->config = $config;
    }

    /**
     *  Unset indexer data in configuration if flat is disabled
     *
     * @param \Magento\Indexer\Model\Config\Data $subject
     * @param callable $proceed
     * @param string $path
     * @param mixed $default
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGet(
        \Magento\Indexer\Model\Config\Data $subject,
        \Closure $proceed,
        $path = null,
        $default = null
    ) {
        $data = $proceed($path, $default);

        if (!$this->config->isEnabled()) {
            // Process Category indexer data
            $this->processData(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID, $path, $default, $data);
            // Process Product indexer data
            $this->processData(\Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID, $path, $default, $data);
        }

        return $data;
    }

    /**
     * @param int $indexerId
     * @param string $path
     * @param mixed $default
     * @param mixed $data
     * @return void
     */
    protected function processData($indexerId, $path, $default, &$data)
    {
        if (!$path && isset($data[$indexerId])) {
            unset($data[$indexerId]);
        } elseif ($path) {
            list($firstKey,) = explode('/', $path);
            if ($firstKey == $indexerId) {
                $data = $default;
            }
        }
    }
}
