<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Enterprise CustomerSegment Resource Helper Mysql
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerSegment\Model\ResourceModel;

class Helper extends \Magento\Framework\DB\Helper
{
    /**
     * Get comparison condition for rule condition operator which will be used in SQL query
     *
     * @param string $operator
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getSqlOperator($operator)
    {
        /*
            '{}'  => __('contains'),
            '!{}' => __('does not contain'),
            '()'  => __('is one of'),
            '!()' => __('is not one of'),
            requires custom selects
        */

        switch ($operator) {
            case '==':
                return '=';
            case '!=':
                return '<>';
            case '{}':
                return 'LIKE';
            case '!{}':
                return 'NOT LIKE';
            case '()':
                return 'IN';
            case '!()':
                return 'NOT IN';
            case '[]':
                return 'FIND_IN_SET(%s, %s)';
            case '![]':
                return 'FIND_IN_SET(%s, %s) IS NULL';
            case 'between':
                return 'BETWEEN %s AND %s';
            case '>':
            case '<':
            case '>=':
            case '<=':
                return $operator;
            default:
                throw new \Magento\Framework\Exception\LocalizedException(__('Unknown operator specified.'));
        }
    }
}
