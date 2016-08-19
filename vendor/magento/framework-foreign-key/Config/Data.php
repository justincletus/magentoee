<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\ForeignKey\Config;

/**
 * @codeCoverageIgnore
 */
class Data extends \Magento\Framework\Config\Data
{
    /**
     * @param Reader $reader
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Reader $reader,
        \Magento\Framework\Config\CacheInterface $cache,
        $cacheId = 'constraint_config_cache'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }
}
