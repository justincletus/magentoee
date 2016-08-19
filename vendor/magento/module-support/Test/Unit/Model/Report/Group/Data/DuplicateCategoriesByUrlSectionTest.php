<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Group\Data;

use Magento\Catalog\Model\Category;

class DuplicateCategoriesByUrlSectionTest extends AbstractDataGroupTest
{
    /**
     * @var string
     */
    protected $reportNamespace = 'Magento\Support\Model\Report\Group\Data\DuplicateCategoriesByUrlSection';

    /**
     * @return void
     */
    public function testGenerate()
    {
        $duplicateUrl = 'test.html';
        $entityTypeId = 1;
        $nameAttributeId = 2;
        $urlKeyAttributeId = 3;
        $entityId = 4;
        $entityName = 'Test Category';
        $storeId = 5;
        $storeName = 'Default';
        $eavTable = 'eav_attribute';
        $entityVarcharTable = 'catalog_category_entity_varchar';
        $storeTable = 'store';
        $whereString = '`u`.`value` = "' . $duplicateUrl . '"';

        $this->entityTypeTest(Category::ENTITY, $entityTypeId);

        $this->connectionMock->expects($this->any())
            ->method('getTableName')
            ->willReturnMap([
                [$eavTable, $eavTable],
                [$entityVarcharTable, $entityVarcharTable],
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
        $sqlGetDuplicateInfo = 'SELECT `u`.`entity_id`, `n`.`value` AS `name`,'
            . ' `u`.`store_id`, `s`.`name` AS `store_name`'
            . ' FROM `' . $entityVarcharTable . '` u'
            . ' LEFT JOIN `' . $storeTable . '` s ON `u`.`store_id` = `s`.`store_id`'
            . ' LEFT JOIN `' . $entityVarcharTable . '` n ON `u`.`entity_id` = `n`.`entity_id` AND'
            . ' `u`.`store_id` = `n`.`store_id` AND'
            . ' `n`.attribute_id = ' . $nameAttributeId
            . ' WHERE `u`.`attribute_id` = ' . $urlKeyAttributeId
            . ' AND ' . $whereString;
        $duplicateInfo = [
            [
                'entity_id' => $entityId,
                'name' => $entityName,
                'store_id' => $storeId,
                'store_name' => $storeName
            ]
        ];
        $this->connectionMock->expects($this->any())
            ->method('fetchAll')
            ->willReturnMap([
                [$sqlGetDuplicateList, [], null, $duplicateList],
                [$sqlGetDuplicateInfo, [], null, $duplicateInfo]
            ]);

        $expectedResult = $this->getExpectedResult([
            [$entityId, $duplicateUrl, $entityName, $storeName . ' {ID:' . $storeId . '}']
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
            (string)__('Duplicate Categories By URL Key') => [
                'headers' => [__('Id'), __('URL key'), __('Name'), __('Store')],
                'data' => $data
            ]
        ];
    }
}
