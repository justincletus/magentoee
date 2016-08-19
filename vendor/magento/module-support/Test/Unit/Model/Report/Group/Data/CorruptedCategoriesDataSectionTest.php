<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Group\Data;

class CorruptedCategoriesDataSectionTest extends AbstractDataGroupTest
{
    /**
     * @var string
     */
    protected $reportNamespace = 'Magento\Support\Model\Report\Group\Data\CorruptedCategoriesDataSection';

    /**
     * @return void
     */
    public function testGenerate()
    {
        $tableName = 'catalog_category_entity';
        $sqlForExpectedData = 'SELECT `c`.`entity_id`, COUNT(`c2`.`children_count`) AS `children_count`,'
            . ' (LENGTH(`c`.`path`) - LENGTH(REPLACE(`c`.`path`,\'/\',\'\'))) AS `level`'
            . ' FROM `' . $tableName . '` AS `c`'
            . ' LEFT JOIN `' . $tableName . '` AS `c2` ON `c2`.`path` LIKE CONCAT(`c`.`path`,\'/%\')'
            . ' GROUP BY c.path';
        $sqlForActualData = 'SELECT `entity_id`, `children_count`, `level` FROM `' . $tableName . '`';
        $expectedData = [
            ['entity_id' => 1, 'children_count' => 0, 'level' => 1],
            ['entity_id' => 3, 'children_count' => 0, 'level' => 1],
            ['entity_id' => 4, 'children_count' => 2, 'level' => 3]
        ];
        $actualData = [
            ['entity_id' => 1, 'children_count' => 0, 'level' => 1],
            ['entity_id' => 2, 'children_count' => 0, 'level' => 1],
            ['entity_id' => 3, 'children_count' => 1, 'level' => 2],
            ['entity_id' => 4, 'children_count' => 0, 'level' => 1]
        ];
        $expectedResult = $this->getExpectedResult([
            [2, 'n/a', 0, 'n/a', 1],
            [3, 0, '1 (diff: +1)', 1, '2 (diff: +1)'],
            [4, 2, '0 (diff: -2)', 3, '1 (diff: -2)']
        ]);

        $this->connectionMock->expects($this->once())
            ->method('getTableName')
            ->with($tableName)
            ->willReturn($tableName);
        $this->connectionMock->expects($this->any())
            ->method('fetchAll')
            ->willReturnMap([
                [$sqlForExpectedData, [], null, $expectedData],
                [$sqlForActualData, [], null, $actualData]
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
            (string)__('Corrupted Categories Data') => [
                'headers' => [
                    __('Id'), __('Expected Children Count'), __('Actual Children Count'),
                    __('Expected Level'), __('Actual Level')
                ],
                'data' => $data
            ]
        ];
    }
}
