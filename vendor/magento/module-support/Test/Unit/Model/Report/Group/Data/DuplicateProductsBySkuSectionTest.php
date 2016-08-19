<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Group\Data;

use Magento\Catalog\Model\Product;

class DuplicateProductsBySkuSectionTest extends AbstractDataGroupTest
{
    /**
     * @var string
     */
    protected $reportNamespace = 'Magento\Support\Model\Report\Group\Data\DuplicateProductsBySkuSection';

    /**
     * @return void
     */
    public function testGenerate()
    {
        $duplicateSku = 'testSKU';
        $entityTypeId = 1;
        $nameAttributeId = 2;
        $entityId = 3;
        $entityName = 'Sample Product';
        $eavTable = 'eav_attribute';
        $entityVarcharTable = 'catalog_product_entity_varchar';
        $entityTable = 'catalog_product_entity';
        $whereString = '`e`.`sku` = "' . $duplicateSku . '"';

        $this->entityTypeTest(Product::ENTITY, $entityTypeId);

        $this->connectionMock->expects($this->any())
            ->method('getTableName')
            ->willReturnMap([
                [$eavTable, $eavTable],
                [$entityTable, $entityTable],
                [$entityVarcharTable, $entityVarcharTable]
            ]);

        $this->connectionMock->expects($this->once())
            ->method('fetchOne')
            ->with($this->getSqlAttributeId('name', $eavTable, $entityTypeId))
            ->willReturn($nameAttributeId);

        $this->connectionMock->expects($this->once())
            ->method('quoteInto')
            ->with('`e`.`sku` = ?', $duplicateSku, null, null)
            ->willReturn($whereString);

        $sqlGetDuplicateList = 'SELECT COUNT(1) AS `cnt`, `sku`'
            . ' FROM `' . $entityTable . '`'
            . ' GROUP BY `sku`'
            . ' HAVING `cnt` > 1'
            . ' ORDER BY `cnt` DESC, `entity_id`';
        $duplicateList = [['cnt' => 2, 'sku' => $duplicateSku]];
        $sqlGetDuplicateInfo = 'SELECT `e`.`entity_id`, `n`.`value` AS `name`, `e`.`sku`'
            . ' FROM `' . $entityTable . '` e'
            . ' LEFT JOIN `' . $entityVarcharTable . '` n'
            . ' ON `e`.`entity_id` = `n`.`entity_id` AND `n`.attribute_id = ' . $nameAttributeId
            . ' WHERE ' . $whereString;
        $duplicateInfo = [
            [
                'entity_id' => $entityId,
                'name' => $entityName,
                'sku' => $duplicateSku
            ]
        ];
        $this->connectionMock->expects($this->any())
            ->method('fetchAll')
            ->willReturnMap([
                [$sqlGetDuplicateList, [], null, $duplicateList],
                [$sqlGetDuplicateInfo, [], null, $duplicateInfo]
            ]);

        $expectedResult = $this->getExpectedResult([
            [$entityId, $duplicateSku, $entityName]
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
            (string)__('Duplicate Products By SKU') => [
                'headers' => [__('Id'), __('SKU'), __('Name')],
                'data' => $data
            ]
        ];
    }
}
