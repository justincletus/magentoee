<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCatalog\Model\Indexer\Table;

/**
 * Class Strategy
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Strategy extends \Magento\Framework\Indexer\Table\Strategy
{
    const TEMP_SUFFIX = '_temp';

    /**
     * Prepare index table name
     *
     * @param string $tablePrefix
     *
     * @return string
     */
    public function prepareTableName($tablePrefix)
    {
        if ($this->getUseIdxTable()) {
            return $tablePrefix . self::IDX_SUFFIX;
        } else {
            $this->resource->getConnection('indexer')->createTemporaryTableLike(
                $this->resource->getTableName($tablePrefix . self::TEMP_SUFFIX),
                $this->resource->getTableName($tablePrefix . self::TMP_SUFFIX),
                true
            );
            return $tablePrefix . self::TEMP_SUFFIX;
        }
    }
}
