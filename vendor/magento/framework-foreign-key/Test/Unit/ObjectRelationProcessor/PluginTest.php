<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ForeignKey\Test\Unit\ObjectRelationProcessor;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ForeignKey\ObjectRelationProcessor\Plugin
     */
    protected $model;

    /**
     * @var \Magento\Framework\ForeignKey\ConfigInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Framework\ForeignKey\ConstraintProcessor | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $constraintProcessorMock;


    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $constraintsMock;

    protected function setUp()
    {
        $this->configMock = $this->getMock(
            'Magento\Framework\ForeignKey\ConfigInterface',
            [],
            [],
            '',
            false
        );

        $this->constraintProcessorMock = $this->getMock(
            'Magento\Framework\ForeignKey\ConstraintProcessor',
            [],
            [],
            '',
            false
        );

        $this->subjectMock = $this->getMock(
            'Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor',
            [],
            [],
            '',
            false
        );
        $this->transactionManagerMock = $this->getMock(
            'Magento\Framework\Model\ResourceModel\Db\TransactionManagerInterface',
            [],
            [],
            '',
            false
        );
        $this->connectionMock = $this->getMock('Magento\Framework\DB\Adapter\AdapterInterface', [], [], '', false);
        $this->constraintsMock = $this->getMock(
            'Magento\Framework\ForeignKey\ConstraintInterface',
            [],
            [],
            '',
            false
        );

        $this->model = new \Magento\Framework\ForeignKey\ObjectRelationProcessor\Plugin(
            $this->configMock,
            $this->constraintProcessorMock
        );
    }

    public function testBeforeDelete()
    {
        $selectMock = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);
        $this->connectionMock->expects($this->once())->method('select')->willReturn($selectMock);
        $selectMock->expects($this->once())->method('forUpdate')->with(true)->willReturnSelf();
        $selectMock->expects($this->once())->method('from')->with('table_name')->willReturnSelf();
        $selectMock->expects($this->once())->method('where')->with('condition')->willReturnSelf();
        $this->connectionMock->expects($this->once())->method('fetchAssoc')->with($selectMock);
        $this->configMock->expects($this->once())
            ->method('getConstraintsByReferenceTableName')
            ->with('table_name')
            ->willReturn([$this->constraintsMock]);
        $this->constraintProcessorMock->expects($this->once())
            ->method('resolve')
            ->with($this->transactionManagerMock, $this->constraintsMock, [[]]);
        $this->model->beforeDelete(
            $this->subjectMock,
            $this->transactionManagerMock,
            $this->connectionMock,
            'table_name',
            'condition',
            []
        );
    }

    public function testBeforeValidateDataIntegrityForNativeDBConstraints()
    {
        $this->configMock->expects($this->once())
            ->method('getConstraintsByTableName')
            ->with('table_name')
            ->willReturn([$this->constraintsMock]);
        $this->constraintsMock->expects($this->once())->method('getStrategy')->willReturn('DB ');

        $this->constraintProcessorMock->expects($this->never())->method('validate');
        $this->model->beforeValidateDataIntegrity($this->subjectMock, 'table_name', []);
    }

    public function testBeforeValidateDataIntegrity()
    {
        $this->configMock->expects($this->once())
            ->method('getConstraintsByTableName')
            ->with('table_name')
            ->willReturn([$this->constraintsMock]);
        $this->constraintsMock->expects($this->once())->method('getStrategy')->willReturn('notDB');

        $this->constraintProcessorMock->expects($this->once())->method('validate')->with($this->constraintsMock, []);
        $this->model->beforeValidateDataIntegrity($this->subjectMock, 'table_name', []);
    }
}
