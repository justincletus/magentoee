<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VisualMerchandiser\Block\Adminhtml\Widget\Tile\Attribute;


class Factory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $attributeCode
     * @return \Magento\VisualMerchandiser\Block\Adminhtml\Widget\Tile\Attribute\Renderer
     */
    public function create($attributeCode)
    {
        $attributeCode = strtolower($attributeCode);
        $renderers = $this->_prepareAttributeRenderers();

        if (isset($renderers[$attributeCode])) {
            $renderer = $this->objectManager->create($renderers[$attributeCode]['renderer']);
        } else {
            $renderer = $this->objectManager->create(
                '\Magento\VisualMerchandiser\Block\Adminhtml\Widget\Tile\Attribute\Renderer'
            );
        }

        return $renderer;
    }

    /**
     * Formatted as 'attribute_code' => [config]
     * @return array
     */
    protected function _prepareAttributeRenderers()
    {
        $renderers = [
            'price' => [
                'renderer' => '\Magento\VisualMerchandiser\Block\Adminhtml\Widget\Tile\Attribute\Renderer\Price'
            ],
            'stock' => [
                'renderer' => '\Magento\VisualMerchandiser\Block\Adminhtml\Widget\Tile\Attribute\Renderer\Stock'
            ],
            'name' => [
                'renderer' => '\Magento\VisualMerchandiser\Block\Adminhtml\Widget\Tile\Attribute\Renderer\Name'
            ]
        ];
        return $renderers;
    }
}
