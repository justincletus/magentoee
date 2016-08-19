<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistrySampleData\Setup;

use Magento\Framework\Setup;

class Installer implements Setup\SampleData\InstallerInterface
{
    /**
     * @var \Magento\GiftRegistrySampleData\Model\GiftRegistry $giftRegistry
     */
    protected $giftRegistry;

    /**
     * @param \Magento\GiftRegistrySampleData\Model\GiftRegistry $giftRegistry
     */
    public function __construct(\Magento\GiftRegistrySampleData\Model\GiftRegistry $giftRegistry)
    {
        $this->giftRegistry = $giftRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->giftRegistry->install(['Magento_GiftRegistrySampleData::fixtures/gift_registry.csv']);
    }
}