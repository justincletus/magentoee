<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Group\Data;

use Magento\Catalog\Model\Product;

class DuplicateProductsByUrlSectionTest extends AbstractDataGroupTest
{
    /**
     * @var string
     */
    protected $reportNamespace = 'Magento\Support\Model\Report\Group\Data\DuplicateProductsByUrlSection';

    /**
     * @return void
     */
    public function testGenerate()
    {
        $duplicateUrl = 'test.html';
        $entityTypeId = 1;
        $nameAttributeId = 2;
        $urlKeyAttributeId = 3;
        $entityId = 5;
        $entityName = 'Sample Product';
        $entityIdSecond = 6;
        $entityNameSecond = 'Second Sample Product';
        $storeId = 6;
        $storeName = 'Default';
        $websiteId = 7;
        $websiteName = 'Default Website';
        $eavTable = 'eav_attribute';
        $entityVarcharTable = 'catalog_product_entity_varchar';
        $productWebsiteTable = 'catalog_product_website';
        $storeWebsiteTable = 'store_website';
        $storeTable = 'store';
        $whereString = '`u`.`value` = "' . $duplicateUrl . '"';

        $this->entityTypeTest(Product::ENTITY, $entityTypeId);

        $this->connectionMock->expects($this->any())
            ->method('getTableName')
            ->willReturnMap([
                [$eavTable, $eavTable],
                [$entityVarcharTable, $entityVarcharTable],
                [$productWebsiteTable, $productWebsiteTable],
                [$storeWebsiteTable, $storeWebsiteTable],
                [$storeTable, $storeTable]
            ]);

        $this->connectionMock->expects($this->any())
            ->method('fetchOne')
            ->willReturnMap([
                [$this->getSqlAttributeId('name', $eavTable, $entityTypeId), [], $nameAttributeId],
                [$this->getSqlAttributeId('url_key', $eavTable, $entityTypeId), [], $urlKeyAttributeId]
            ]);

        $this->connectionMock->expects($this->once())
            ->method('quoteInto')
            ->with('`u`.`value` = ?', $duplicateUrl, null, null)
            ->willReturn($whereString);

        $sqlGetDuplicateList = 'SELECT COUNT(1) AS `cnt`, `value`'
            . ' FROM `' . $entityVarcharTable . '`'
            . ' WHERE `attribute_id` = ' . $urlKeyAttributeId
            . ' GROUP BY `value`'
            . ' HAVING `cnt` > 1'
            . ' ORDER BY `cnt` DESC, `entity_id`';
        $duplicateList = [['cnt' => 2, 'value' => $duplicateUrl]];
        $sqlGetDuplicateInfo = 'SELECT `u`.`entity_id`, `n`.`value` AS `name`, `u`.`store_id`,'
            . ' `s`.`name` AS `store_name`, `pw`.`website_id`, `sw`.`name` AS `website_name`'
            . ' FROM `' . $entityVarcharTable . '` AS `u`'
            . ' LEFT JOIN `' . $productWebsiteTable . '` AS `pw` ON `u`.`entity_id` = `pw`.`product_id`'
            . ' LEFT JOIN `' . $storeWebsiteTable . '` AS `sw` ON `pw`.`website_id` = `sw`.`website_id`'
            . ' LEFT JOIN `' . $storeTable . '` s ON `u`.`store_id` = `s`.`store_id`'
            . ' LEFT JOIN `' . $entityVarcharTable . '` AS `n` ON `u`.`entity_id` = `n`.`entity_id` AND'
            . ' `u`.`store_id` = `n`.`store_id` AND'
            . ' `n`.attribute_id = ' . $nameAttributeId
            . ' WHERE `u`.`attribute_id` = ' . $urlKeyAttributeId . ' AND ' . $whereString;
        $duplicateInfo = [
            [
                'entity_id' => $entityId,
                'name' => $entityName,
                'store_id' => $storeId,
                'store_name' => $storeName,
                'website_id' => $websiteId,
                'website_name' => $websiteName
            ],
            [
                'entity_id' => $entityIdSecond,
                'name' => $entityNameSecond,
                'store_id' => null,
                'store_name' => null,
                'website_id' => null,
                'website_name' => null
            ]
        ];
        $this->connectionMock->expects($this->any())
            ->method('fetchAll')
            ->willReturnMap([
                [$sqlGetDuplicateList, [], null, $duplicateList],
                [$sqlGetDuplicateInfo, [], null, $duplicateInfo]
            ]);

        $expectedResult = $this->getExpectedResult([
            [
                $entityId, $duplicateUrl, $entityName,
                $websiteName . ' {ID:' . $websiteId . '}', $storeName . ' {ID:' . $storeId . '}'
            ],
            [$entityIdSecond, $duplicateUrl, $entityNameSecond, 'Not select', 'All']
        ]);

        $this->assertEquals($expectedResult, $this->report->generate());
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getExpectedResult($data = [])
    {
        return [
            (string)__('Duplicate Products By URL Key') => [
                'headers' => [__('Id'), __('URL Key'), __('Name'), __('Website'), __('Store')],
                'data' => $data
            ]
        ];
    }
}
