<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogPermissions\Test\Unit\Model\Adminhtml\System\Config\Source\Customer;

use Magento\CatalogPermissions\Model\Adminhtml\System\Config\Source\Customer\Group;

/**
 * Unit test for Magento\CatalogPermissions\Model\Adminhtml\System\Config\Source\Customer\Group
 */
class GroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogPermissions\Model\Adminhtml\System\Config\Source\Customer\Group
     */
    protected $group;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactoryMock;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->collectionFactoryMock = $this->getMock(
            'Magento\Customer\Model\ResourceModel\Group\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->group = new Group($this->collectionFactoryMock);
    }

    /**
     * @return void
     */
    public function testToOptionArray()
    {
        $collectionMock = $this->getMockBuilder('Magento\Customer\Model\ResourceModel\Group\Collection')
            ->disableOriginalConstructor()
            ->getMock();

        $this->collectionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $collectionMock
            ->expects($this->once())
            ->method('loadData')
            ->willReturnSelf();
        $collectionMock
            ->expects($this->once())
            ->method('toOptionArray')
            ->willReturn(['foo', 'bar']);

        $this->assertEquals(['foo', 'bar'], $this->group->toOptionArray());
    }
}
