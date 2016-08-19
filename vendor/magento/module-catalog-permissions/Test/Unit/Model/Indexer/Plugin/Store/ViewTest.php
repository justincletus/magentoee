<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Test\Unit\Model\Indexer\Plugin\Store;

class ViewTest extends \PHPUnit_Framework_TestCase
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
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Store\Model\ResourceModel\Store
     */
    protected $subjectMock;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerRegistryMock;

    /**
     * @var View
     */
    protected $model;

    protected function setUp()
    {
        $this->subjectMock = $this->getMock('Magento\Store\Model\ResourceModel\Store', [], [], '', false);

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
        $this->model = new \Magento\CatalogPermissions\Model\Indexer\Plugin\Store\View(
            $this->indexerRegistryMock,
            $this->configMock
        );
    }

    public function testAroundSaveNewObject()
    {
        $this->mockIndexerMethods();
        $storeMock = $this->getMock(
            'Magento\Store\Model\Store',
            ['isObjectNew', 'dataHasChangedFor', '__wakeup'],
            [],
            '',
            false
        );
        $storeMock->expects($this->once())->method('isObjectNew')->will($this->returnValue(true));
        $closureMock = function () use ($storeMock) {
            return $this->subjectMock;
        };
        $this->assertEquals(
            $this->subjectMock,
            $this->model->aroundSave($this->subjectMock, $closureMock, $storeMock)
        );
    }

    public function testAroundSaveHasChanged()
    {
        $storeMock = $this->getMock(
            'Magento\Store\Model\Store',
            ['isObjectNew', 'dataHasChangedFor', '__wakeup'],
            [],
            '',
            false
        );
        $closureMock = function () use ($storeMock) {
            return $this->subjectMock;
        };
        $this->assertEquals(
            $this->subjectMock,
            $this->model->aroundSave($this->subjectMock, $closureMock, $storeMock)
        );
    }

    public function testAroundSaveNoNeed()
    {
        $storeMock = $this->getMock(
            'Magento\Store\Model\Store',
            ['isObjectNew', 'dataHasChangedFor', '__wakeup'],
            [],
            '',
            false
        );
        $closureMock = function () use ($storeMock) {
            return $this->subjectMock;
        };
        $this->assertEquals(
            $this->subjectMock,
            $this->model->aroundSave($this->subjectMock, $closureMock, $storeMock)
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Indexer\Model\Indexer\State
     */
    protected function getStateMock()
    {
        $stateMock = $this->getMock(
            'Magento\Indexer\Model\Indexer\State',
            ['setStatus', 'save', '__wakeup'],
            [],
            '',
            false
        );
        $stateMock->expects($this->once())->method('setStatus')->with('invalid')->will($this->returnSelf());
        $stateMock->expects($this->once())->method('save')->will($this->returnSelf());

        return $stateMock;
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
