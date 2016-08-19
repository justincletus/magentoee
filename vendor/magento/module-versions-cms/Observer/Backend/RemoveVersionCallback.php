<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Observer\Backend;

class RemoveVersionCallback
{
    /**
     * Callback function to remove version or change access
     * level to protected if we can't remove it.
     *
     * @param array $args
     * @return void
     */
    public function execute($args)
    {
        $version = $args['version'];
        $version->setData($args['row']);

        try {
            $version->delete();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            /**
             * If we have situation when revision from
             * orphaned private version published we should
             * change its access level to protected so publisher
             * will have chance to see it and assign to some user
             */
            $version->setAccessLevel(\Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PROTECTED);
            $version->save();
        }
    }
}
