<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Test\Unit\Model\Indexer\Plugin\Store;

class GroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Indexer\IndexerInterface
     */
    protected $indexerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\CatalogPermissions\App\ConfigInterface
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Store\Model\ResourceModel\Group
     */
    protected $subjectMock;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerRegistryMock;

    /**
     * @var Group
     */
    protected $model;

    protected function setUp()
    {
        $this->subjectMock = $this->getMock('Magento\Store\Model\ResourceModel\Group', [], [], '', false);
        $this->indexerMock = $this->getMockForAbstractClass(
            'Magento\Framework\Indexer\IndexerInterface',
            [],
            '',
            false,
            false,
            true,
            ['getId', 'getState', '__wakeup']
        );
        $this->configMock = $this->getMockForAbstractClass(
            'Magento\CatalogPermissions\App\ConfigInterface',
            [],
            '',
            false,
            false,
            true,
            ['isEnabled']
        );
        $this->configMock->expects($this->any())->method('isEnabled')->will($this->returnValue(true));
        $this->indexerRegistryMock = $this->getMock(
            'Magento\Framework\Indexer\IndexerRegistry',
            ['get'],
            [],
            '',
            false
        );
        $this->model = new \Magento\CatalogPermissions\Model\Indexer\Plugin\Store\Group(
            $this->indexerRegistryMock,
            $this->configMock
        );
    }

    /**
     * @param array $valueMap
     * @dataProvider changedDataProvider
     */
    public function testAroundSave($valueMap)
    {
        $this->mockIndexerMethods();
        $groupMock = $this->getMock(
            'Magento\Store\Model\Group',
            ['dataHasChangedFor', 'isObjectNew', '__wakeup'],
            [],
            '',
            false
        );
        $groupMock->expects($this->exactly(2))->method('dataHasChangedFor')->will($this->returnValueMap($valueMap));
        $groupMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(false));

        $closureMock = function () use ($groupMock) {
            return $this->subjectMock;
        };
        $this->assertEquals(
            $this->subjectMock,
            $this->model->aroundSave($this->subjectMock, $closureMock, $groupMock)
        );
    }

    /**
     * @param array $valueMap
     * @dataProvider changedDataProvider
     */
    public function testAroundSaveNotNew($valueMap)
    {
        $groupMock = $this->getMock(
            'Magento\Store\Model\Group',
            ['dataHasChangedFor', 'isObjectNew', '__wakeup'],
            [],
            '',
            false
        );
        $groupMock->expects($this->exactly(2))->method('dataHasChangedFor')->will($this->returnValueMap($valueMap));
        $groupMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(true));
        $closureMock = function () use ($groupMock) {
            return $this->subjectMock;
        };
        $this->assertEquals(
            $this->subjectMock,
            $this->model->aroundSave($this->subjectMock, $closureMock, $groupMock)
        );
    }

    public function changedDataProvider()
    {
        return [
            [
                [['root_category_id', true], ['website_id', false]],
                [['root_category_id', false], ['website_id', true]],
            ]
        ];
    }

    public function testAroundSaveWithoutChanges()
    {
        $groupMock = $this->getMock(
            'Magento\Store\Model\Group',
            ['dataHasChangedFor', 'isObjectNew', '__wakeup'],
            [],
            '',
            false
        );
        $groupMock->expects(
            $this->exactly(2)
        )->method(
            'dataHasChangedFor'
        )->will(
            $this->returnValueMap([['root_category_id', false], ['website_id', false]])
        );
        $groupMock->expects($this->never())->method('isObjectNew');

        $closureMock = function () use ($groupMock) {
            return $this->subjectMock;
        };
        $this->assertEquals(
            $this->subjectMock,
            $this->model->aroundSave($this->subjectMock, $closureMock, $groupMock)
        );
    }

    protected function mockIndexerMethods()
    {
        $this->indexerMock->expects($this->once())->method('invalidate');
        $this->indexerRegistryMock->expects($this->once())
            ->method('get')
            ->with(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID)
            ->will($this->returnValue($this->indexerMock));
    }
}
