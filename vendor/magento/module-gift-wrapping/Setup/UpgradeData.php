<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftWrapping\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }
    
    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\Quote\Setup\QuoteSetup $quoteInstaller */
        $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

        /** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.1') < 0) {
            $entityAttributesCodes = [
                'gw_base_price_incl_tax' => 'decimal',
                'gw_price_incl_tax' => 'decimal',
                'gw_items_base_price_incl_tax' => 'decimal',
                'gw_items_price_incl_tax' => 'decimal',
                'gw_card_base_price_incl_tax' => 'decimal',
                'gw_card_price_incl_tax' => 'decimal',
            ];
            foreach ($entityAttributesCodes as $code => $type) {
                $quoteInstaller->addAttribute('quote', $code, ['type' => $type, 'visible' => false]);
                $quoteInstaller->addAttribute('quote_address', $code, ['type' => $type, 'visible' => false]);
                $salesInstaller->addAttribute('order', $code, ['type' => $type, 'visible' => false]);
            }
        }
        $setup->endSetup();
    }
}
