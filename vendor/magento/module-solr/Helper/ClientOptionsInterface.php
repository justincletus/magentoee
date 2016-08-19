<?php
/**
 * Search client helper interface
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\Helper;

interface ClientOptionsInterface
{
    /**
     * Return search client options
     *
     * @param array $options
     * @return mixed
     */
    public function prepareClientOptions($options = []);
}
