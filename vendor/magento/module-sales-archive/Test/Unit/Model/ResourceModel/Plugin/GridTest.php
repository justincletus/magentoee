<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesArchive\Test\Unit\Model\ResourceModel\Plugin;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class GridTest
 */
class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\GridPool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $gridPoolSource;

    /**
     * @var \Magento\SalesArchive\Model\ResourceModel\Archive|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $archiveSource;

    /**
     * @var \Magento\Framework\App\ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resource;

    /**
     * @var \Magento\SalesArchive\Model\ResourceModel\Plugin\Grid
     */
    protected $plugin;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->gridPoolSource = $this->getMock('Magento\Sales\Model\ResourceModel\GridPool', [], [], '', false);
        $this->archiveSource = $this->getMock('Magento\SalesArchive\Model\ResourceModel\Archive', [], [], '', false);
        $this->resource = $this->getMock('Magento\Framework\App\ResourceConnection', [], [], '', false);

        $this->plugin = $objectManager->getObject(
            'Magento\SalesArchive\Model\ResourceModel\Plugin\Grid',
            [
                'gridPool' => $this->gridPoolSource,
                'archive' => $this->archiveSource,
                'resource' => $this->resource
            ]
        );
    }

    public function testAroundRefreshOrderInArchive()
    {
        $grid = $this->getMock(
            'Magento\Sales\Model\ResourceModel\Grid',
            [],
            [],
            '',
            false
        );
        $grid->expects($this->once())
            ->method('getGridTable')
            ->willReturn('sales_order');
        $this->resource->expects($this->once())
            ->method('getTableName')
            ->willReturn('sales_order');

        $value = '15';
        $field = null;
        $callable = function ($value, $field) {
            return true;
        };

        $this->archiveSource->expects($this->once())
            ->method('isOrderInArchive')
            ->with($value)
            ->willReturn(true);

        $this->archiveSource->expects($this->once())
            ->method('removeOrdersFromArchiveById')
            ->with([$value]);

        $this->gridPoolSource->expects($this->once())
            ->method('refreshByOrderId')
            ->with($value)
            ->willReturn(true);
        $this->assertTrue($this->plugin->aroundRefresh($grid, $callable, $value, $field));
    }

    public function testAroundRefreshOrderNotInArchive()
    {
        $grid = $this->getMock(
            'Magento\Sales\Model\ResourceModel\Grid',
            [],
            [],
            '',
            false
        );
        $grid->expects($this->once())
            ->method('getGridTable')
            ->willReturn('sales_order');
        $this->resource->expects($this->once())
            ->method('getTableName')
            ->willReturn('sales_order');

        $callable = function ($value, $field) {
            return true;
        };
        $value = '15';
        $field = null;

        $this->archiveSource->expects($this->once())
            ->method('isOrderInArchive')
            ->with($value)
            ->will(
                $this->returnValue(false)
            );

        $this->archiveSource->expects($this->never())
            ->method('removeOrdersFromArchiveById');

        $this->gridPoolSource->expects($this->never())
            ->method('refreshByOrderId');
        $this->assertTrue($this->plugin->aroundRefresh($grid, $callable, $value, $field));
    }
}
