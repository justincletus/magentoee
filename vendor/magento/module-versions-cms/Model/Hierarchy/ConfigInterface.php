<?php
/**
 * CMS menu hierarchy configuration model interface
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Model\Hierarchy;

interface ConfigInterface
{
    /**
     * Return available Context Menu layouts output
     *
     * @return array
     */
    public function getAllMenuLayouts();

    /**
     * Return Context Menu layout by its name
     *
     * @param string $layoutName
     * @return \Magento\Framework\DataObject|bool
     */
    public function getContextMenuLayout($layoutName);
}
