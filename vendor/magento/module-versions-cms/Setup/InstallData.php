<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VersionsCms\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Module\Setup\Migration;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Date
     *
     * @var DateTime
     */
    private $coreDate;

    /**
     * Constructor
     *
     * @param DateTime $coreDate
     */
    public function __construct(DateTime $coreDate)
    {
        $this->coreDate = $coreDate;
    }
    
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /*
         * Creating initial versions and revisions
         */
        $attributes = [
            'page_layout',
            'meta_keywords',
            'meta_description',
            'content',
            'layout_update_xml',
            'custom_theme',
            'custom_theme_from',
            'custom_theme_to',
        ];
        $connection = $setup->getConnection();
        $select = $connection->select();

        $select->from(
            ['p' => $setup->getTable('cms_page')]
        )->joinLeft(
            ['v' => $setup->getTable('magento_versionscms_page_version')],
            'v.page_id = p.page_id',
            []
        )->where(
            'v.page_id IS NULL'
        );

        $resource = $connection->query($select);

        while (true == ($page = $resource->fetch(\Zend_Db::FETCH_ASSOC))) {
            $connection->insert(
                $setup->getTable('magento_versionscms_increment'),
                ['increment_type' => 0, 'increment_node' => $page['page_id'], 'increment_level' => 0, 'last_id' => 1]
            );

            $connection->insert(
                $setup->getTable('magento_versionscms_page_version'),
                [
                    'version_number' => 1,
                    'page_id' => $page['page_id'],
                    'access_level' => \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PUBLIC,
                    'user_id' => new \Zend_Db_Expr('NULL'),
                    'revisions_count' => 1,
                    'label' => $page['title'],
                    'created_at' => $this->coreDate->gmtDate()
                ]
            );

            $versionId = $connection->lastInsertId($setup->getTable('magento_versionscms_page_version'), 'version_id');

            $connection->insert(
                $setup->getTable('magento_versionscms_increment'),
                ['increment_type' => 0, 'increment_node' => $versionId, 'increment_level' => 1, 'last_id' => 1]
            );

            /**
             * Prepare revision data
             */
            $_data = [];

            foreach ($attributes as $attr) {
                $_data[$attr] = $page[$attr];
            }

            $_data['created_at'] = $this->coreDate->gmtDate();
            $_data['user_id'] = new \Zend_Db_Expr('NULL');
            $_data['revision_number'] = 1;
            $_data['version_id'] = $versionId;
            $_data['page_id'] = $page['page_id'];

            $connection->insert($setup->getTable('magento_versionscms_page_revision'), $_data);
        }

        $connection->query($select);
        $setup->endSetup();

        $installer = $setup->createMigrationSetup();
        $setup->startSetup();

        $installer->appendClassAliasReplace(
            'magento_versionscms_page_revision',
            'content',
            Migration::ENTITY_TYPE_BLOCK,
            Migration::FIELD_CONTENT_TYPE_WIKI,
            ['revision_id']
        );
        $installer->appendClassAliasReplace(
            'magento_versionscms_page_revision',
            'layout_update_xml',
            Migration::ENTITY_TYPE_BLOCK,
            Migration::FIELD_CONTENT_TYPE_XML,
            ['revision_id']
        );
        $installer->appendClassAliasReplace(
            'magento_versionscms_page_revision',
            'custom_layout_update_xml',
            Migration::ENTITY_TYPE_BLOCK,
            Migration::FIELD_CONTENT_TYPE_XML,
            ['revision_id']
        );
        $installer->doUpdateClassAliases();

        $setup->endSetup();

    }
}
