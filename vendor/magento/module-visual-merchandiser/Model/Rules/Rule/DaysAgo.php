<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VisualMerchandiser\Model\Rules\Rule;

class DaysAgo extends \Magento\VisualMerchandiser\Model\Rules\Rule
{
    /**
     * Operators map for DaysAgo rule
     *
     * @var array
     */
    protected $operatorMap = [
        'lt' => 'gt',
        'gt' => 'lt',
        'gteq' => 'lteq',
        'lteq' => 'gteq'
    ];

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return void
     */
    public function applyToCollection($collection)
    {
        $value = (int)$this->_rule['value'];
        $this->_rule['operator'] = $this->getOperatorForRule($this->_rule['operator']);
        $dateValue = date('Y-m-d', strtotime('-' . $value . ' days'));

        if ($this->_rule['operator'] == 'eq') {
            $dateStart = date('Y-m-d 00:00:00', strtotime($dateValue));
            $dateEnd = date('Y-m-d 23:59:59', strtotime($dateValue));
            $criteria = [
                'from'  => $dateStart,
                'to' => $dateEnd
            ];
        } else {
            $criteria = [
                $this->_rule['operator'] => $dateValue
            ];
        }
        $collection->addFieldToFilter($this->_rule['attribute'], $criteria);
    }

    /**
     * Return valid operator for rule
     *
     * @param string $operator
     * @return string
     */
    protected function getOperatorForRule($operator)
    {
        return isset($this->operatorMap[$operator]) ? $this->operatorMap[$operator] : $operator;
    }

    /**
     * @return array
     */
    public static function getOperators()
    {
        return [
            'eq' => __('Equal'),
            'gt' => __('Greater than'),
            'gteq' => __('Greater than or equal to'),
            'lt' => __('Less than'),
            'lteq' => __('Less than or equal to')
        ];
    }
}
