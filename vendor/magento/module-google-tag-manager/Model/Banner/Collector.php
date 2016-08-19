<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GoogleTagManager\Model\Banner;

class Collector
{
    /**
     * @var string[]
     */
    protected $bannerIds = [];

    /**
     * @param \Magento\Banner\Block\Widget\Banner $banner
     * @return $this
     */
    public function addBannerBlock(\Magento\Banner\Block\Widget\Banner $banner)
    {
        $bannerIds = $banner->getBannerIds();
        if (empty($bannerIds)) {
            return $this;
        }
        $bannerIds = explode(',', $bannerIds);
        $this->bannerIds = array_merge($this->bannerIds, $bannerIds);
        $this->bannerIds = array_unique($this->bannerIds);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getBannerIds()
    {
        return $this->bannerIds;
    }
}
