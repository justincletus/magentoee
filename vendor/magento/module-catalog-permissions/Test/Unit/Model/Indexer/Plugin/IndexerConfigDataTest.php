<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Test\Unit\Model\Indexer\Plugin;

class IndexerConfigDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogPermissions\Model\Indexer\Plugin\IndexerConfigData
     */
    protected $model;

    /**
     * @var \Magento\CatalogPermissions\App\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->configMock = $this->getMock(
            'Magento\CatalogPermissions\App\Config',
            ['isEnabled'],
            [],
            '',
            false
        );
        $this->subjectMock = $this->getMock('Magento\Indexer\Model\Config\Data', [], [], '', false);

        $this->model = new \Magento\CatalogPermissions\Model\Indexer\Plugin\IndexerConfigData($this->configMock);
    }

    /**
     * @param bool $isEnabled
     * @param string $path
     * @param mixed $default
     * @param array $inputData
     * @param array $outputData
     * @dataProvider aroundGetDataProvider
     */
    public function testAroundGet($isEnabled, $path, $default, $inputData, $outputData)
    {
        $closureMock = function () use ($inputData) {
            return $inputData;
        };
        $this->configMock->expects($this->any())->method('isEnabled')->will($this->returnValue($isEnabled));

        $this->assertEquals($outputData, $this->model->aroundGet($this->subjectMock, $closureMock, $path, $default));
    }

    public function aroundGetDataProvider()
    {
        $categoryIndexerData = [
            'indexer_id' => \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID,
            'action' => '\Action\Class',
            'title' => 'Title',
            'description' => 'Description',
        ];
        $productIndexerData = [
            'indexer_id' => \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID,
            'action' => '\Action\Class',
            'title' => 'Title',
            'description' => 'Description',
        ];

        return [
            [
                true,
                null,
                null,
                [
                    \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID => $categoryIndexerData,
                    \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID => $productIndexerData
                ],
                [
                    \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID => $categoryIndexerData,
                    \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID => $productIndexerData
                ],
            ],
            [
                false,
                null,
                null,
                [
                    \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID => $categoryIndexerData,
                    \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID => $productIndexerData
                ],
                []
            ],
            [
                false,
                \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID,
                null,
                $categoryIndexerData,
                null
            ],
            [
                false,
                \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID,
                null,
                $productIndexerData,
                null
            ]
        ];
    }
}
