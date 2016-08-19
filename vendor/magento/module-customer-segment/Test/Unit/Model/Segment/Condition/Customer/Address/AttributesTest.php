<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerSegment\Test\Unit\Model\Segment\Condition\Customer\Address;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class AttributesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Subject of testing.
     *
     * @var \Magento\CustomerSegment\Model\Segment\Condition\Customer\Address\Attributes
     */
    protected $subject;

    /**
     * @var \Magento\CustomerSegment\Model\ResourceModel\Segment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceSegmentMock;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Address|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceAddressMock;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeMock;

    /**
     * @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eavConfigMock;

    /**
     * @var \Magento\Framework\DB\Select|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $selectMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resourceSegmentMock = $this->getMock(
            'Magento\CustomerSegment\Model\ResourceModel\Segment',
            ['createSelect', 'createConditionSql'],
            [],
            '',
            false
        );

        $this->selectMock = $this->getMock(
            'Magento\Framework\DB\Select',
            ['from', 'where', 'limit'],
            [],
            '',
            false
        );

        $this->resourceSegmentMock->expects($this->any())
            ->method('createSelect')
            ->willReturn($this->selectMock);

        $this->resourceAddressMock = $this->getMock(
            'Magento\Customer\Model\ResourceModel\Address',
            ['loadAllAttributes'],
            [],
            '',
            false
        );

        $eavEntity = $this->getMockForAbstractClass(
            'Magento\Eav\Model\Entity\AbstractEntity',
            [],
            '',
            false,
            false,
            true,
            ['getAttributesByCode']
        );

        $eavEntity->expects($this->any())
            ->method('getAttributesByCode')
            ->willReturn([]);

        $this->resourceAddressMock->expects($this->any())
            ->method('loadAllAttributes')
            ->willReturn($eavEntity);

        $this->eavConfigMock = $this->getMock(
            'Magento\Eav\Model\Config',
            ['getAttribute'],
            [],
            '',
            false
        );

        $this->attributeMock = $this->getMock(
            'Magento\Eav\Model\Entity\Attribute',
            ['isStatic', 'getBackendTable', 'getAttributeCode', 'getId'],
            [],
            '',
            false
        );

        $this->attributeMock->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $this->attributeMock->expects($this->any())
            ->method('getBackendTable')
            ->willReturn('data_table');

        $this->attributeMock->expects($this->any())
            ->method('getAttributeCode')
            ->willReturn('country');

        $this->eavConfigMock->expects($this->any())
            ->method('getAttribute')
            ->with('customer_address', 'country')
            ->willReturn($this->attributeMock);

        $this->subject = $objectManager->getObject(
            'Magento\CustomerSegment\Model\Segment\Condition\Customer\Address\Attributes',
            [
                'resourceSegment' => $this->resourceSegmentMock,
                'resourceAddress' => $this->resourceAddressMock,
                'eavConfig' => $this->eavConfigMock
            ]
        );

        $this->subject->setData('attribute', 'country');
        $this->subject->setData('operator', '==');
        $this->subject->setData('value', 'US');
    }

    /**
     * @param bool $customer
     * @param bool $isFiltered
     * @param bool|null $isStaticAttribute
     * @dataProvider getConditionsSqlDataProvider
     * @return void
     */
    public function testGetConditionsSql($customer, $isFiltered, $isStaticAttribute)
    {
        if (!$customer && $isFiltered) {
            $this->selectMock->expects($this->once())
                ->method('from')
                ->with(['' => new \Zend_Db_Expr('dual')], [new \Zend_Db_Expr(0)]);

            $this->selectMock->expects($this->never())
                ->method('where');

            $this->selectMock->expects($this->never())
                ->method('limit');
        } else {
            $this->attributeMock->expects($this->any())
                ->method('isStatic')
                ->willReturn($isStaticAttribute);

            $this->selectMock->expects($this->once())
                ->method('from')
                ->with(['val' => 'data_table'], [new \Zend_Db_Expr(1)]);

            $column = $isStaticAttribute ? "`val`.`country`" : "`val`.`value`";
            $condition = "$column = 'US'";

            $this->resourceSegmentMock->expects($this->any())
                ->method('createConditionSql')
                ->with($column, '==', 'US')
                ->willReturn($condition);

            if (!$isStaticAttribute) {
                $this->selectMock->expects($this->at(1))
                    ->method('where')
                    ->with("`val`.`attribute_id` = ?", 1);

                $this->selectMock->expects($this->at(2))
                    ->method('where')
                    ->with("`val`.`entity_id` = `customer_address`.`entity_id`");

                $this->selectMock->expects($this->at(3))
                    ->method('where')
                    ->with($condition);
            } else {
                $this->selectMock->expects($this->at(1))
                    ->method('where')
                    ->with("`val`.`entity_id` = `customer_address`.`entity_id`");

                $this->selectMock->expects($this->at(2))
                    ->method('where')
                    ->with($condition);
            }

            if ($isFiltered) {
                $this->selectMock->expects($this->once())
                    ->method('limit')
                    ->with(1);
            }
        }

        $this->subject->getConditionsSql($customer, 1, $isFiltered);
    }

    /**
     * @return array
     */
    public function getConditionsSqlDataProvider()
    {
        return [
            [false, true, null],
            [true, true, true],
            [true, false, true],
            [true, false, false]
        ];
    }
}
