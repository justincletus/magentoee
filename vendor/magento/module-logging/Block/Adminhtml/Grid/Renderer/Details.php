<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Difference columns renderer
 *
 */
namespace Magento\Logging\Block\Adminhtml\Grid\Renderer;

class Details extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render the grid cell value
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $html = '-';
        $columnData = $row->getData($this->getColumn()->getIndex());
        try {
            $dataArray = unserialize($columnData);
            if (is_bool($dataArray)) {
                $html = $dataArray ? 'true' : 'false';
            } elseif (is_array($dataArray)) {
                if (isset($dataArray['general'])) {
                    if (!is_array($dataArray['general'])) {
                        $dataArray['general'] = [$dataArray['general']];
                    }
                    $html = $this->escapeHtml(implode(', ', $dataArray['general']));
                }
                /**
                 *  [additional] => Array
                 *          (
                 *               [\Magento\Sales\Model\Order] => Array
                 *                  (
                 *                      [68] => Array
                 *                          (
                 *                              [increment_id] => 100000108,
                 *                              [grand_total] => 422.01
                 *                          )
                 *                      [94] => Array
                 *                          (
                 *                              [increment_id] => 100000121,
                 *                              [grand_total] => 492.77
                 *                          )
                 *
                 *                  )
                 *
                 *          )
                 */
                if (isset($dataArray['additional'])) {
                    $html .= '<br /><br />';
                    foreach ($dataArray['additional'] as $modelName => $modelsData) {
                        foreach ($modelsData as $mdoelId => $data) {
                            $html .= $this->escapeHtml(implode(', ', $data));
                        }
                    }
                }
            } else {
                $html = $columnData;
            }
        } catch (\Exception $e) {
            $html = $columnData;
        }
        return $html;
    }
}
