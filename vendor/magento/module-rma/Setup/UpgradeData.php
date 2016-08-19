<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Rma\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * Sales setup factory
     *
     * @var RmaSetupFactory
     */
    protected $rmaSetupFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @param RmaSetupFactory $rmaSetupFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        RmaSetupFactory $rmaSetupFactory,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->rmaSetupFactory = $rmaSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var RmaSetup $rmaSetup */
        $rmaSetup = $this->rmaSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $rmaSetup->updateEntityType(
                \Magento\Rma\Model\Item::ENTITY,
                'entity_model',
                'Magento\Rma\Model\ResourceModel\Item'
            );
            $rmaSetup->updateEntityType(
                \Magento\Rma\Model\Item::ENTITY,
                'increment_model',
                'Magento\Eav\Model\Entity\Increment\NumericValue'
            );
        }
        $this->eavConfig->clear();
        $setup->endSetup();
    }
}
