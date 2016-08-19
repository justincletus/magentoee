<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\MultipleWishlist\Test\Unit\Model\ResourceModel\Item\Collection;

use Magento\MultipleWishlist\Model\ResourceModel\Item\Collection\Updater;

class UpdaterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Updater|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    /** @var \Magento\Wishlist\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $wishlistHelper;

    public function setUp()
    {
        $this->wishlistHelper = $this->getMock('Magento\Wishlist\Helper\Data', [], [], '', false);
        $this->model = new \Magento\MultipleWishlist\Model\ResourceModel\Item\Collection\Updater($this->wishlistHelper);
    }

    public function testUpdate()
    {
        $connectionMock = $this->getMockForAbstractClass(
            'Magento\Framework\DB\Adapter\AdapterInterface',
            [],
            '',
            false
        );
        $select = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);
        $argument = $this->getMock('Magento\Framework\Data\Collection\AbstractDb', [], [], '', false);
        $argument->expects($this->once())
            ->method('getConnection')
            ->willReturn($connectionMock);
        $this->wishlistHelper->expects($this->once())
            ->method('getDefaultWishlistName')
            ->willReturn('Default Wish List');
        $argument->expects($this->once())
            ->method('getSelect')
            ->willReturn($select);
        $select->expects($this->once())
            ->method('columns')
            ->with(['wishlist_name' => "IFNULL(wishlist.name, 'Default Wish List')"]);
        $connectionMock->expects($this->atLeastOnce())
            ->method('getIfNullSql')
            ->with('wishlist.name', 'Default Wish List')
            ->willReturn("IFNULL(wishlist.name, 'Default Wish List')");
        $argument->expects($this->once())
            ->method('addFilterToMap')
            ->with('wishlist_name', "IFNULL(wishlist.name, 'Default Wish List')");
        $connectionMock->expects($this->atLeastOnce())
            ->method('quote')
            ->with('Default Wish List')
            ->willReturn('Default Wish List');

        $this->assertSame($argument, $this->model->update($argument));
    }
}
