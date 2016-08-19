<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\Model\Client;

interface FactoryInterface
{
    /**
     * Return search client
     *
     * @param array $options
     * @return mixed
     */
    public function create(array $options = []);
}
