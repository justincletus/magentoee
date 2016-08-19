<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\Model;

interface AdapterFactoryInterface
{
    /**
     * Return search adapter
     *
     * @return \Magento\Solr\Model\Adapter\Solarium
     */
    public function createAdapter();
}
