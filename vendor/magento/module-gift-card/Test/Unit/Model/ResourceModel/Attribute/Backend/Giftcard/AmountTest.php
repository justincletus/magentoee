<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCard\Test\Unit\Model\ResourceModel\Attribute\Backend\Giftcard;

class AmountTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCard\Model\ResourceModel\Attribute\Backend\Giftcard\Amount
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;

    protected function setUp()
    {
        $this->connectionMock = $this->getMock('\Magento\Framework\DB\Adapter\AdapterInterface');
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');

        $this->resourceMock = $this->getMock('\Magento\Framework\App\ResourceConnection', [], [], '', false);
        $this->resourceMock->expects($this->once())
            ->method('getTableName')
            ->willReturn('table_name');
        $this->resourceMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($this->connectionMock);

        $contextMock = $this->getMock('\Magento\Framework\Model\ResourceModel\Db\Context', [], [], '', false);
        $contextMock->expects($this->once())->method('getResources')->willReturn($this->resourceMock);

        $this->model = new \Magento\GiftCard\Model\ResourceModel\Attribute\Backend\Giftcard\Amount(
            $contextMock,
            $this->storeManagerMock
        );
    }

    public function testInsertProductData()
    {
        $productId = 100;
        $productMock = $this->getMock('\Magento\Catalog\Model\Product', ['getId'], [], '', false);
        $productMock->expects($this->once())->method('getId')->willReturn($productId);

        $this->connectionMock->expects($this->once())
            ->method('insert')
            ->with('table_name', ['entity_id' => $productId]);
        $this->assertEquals($this->model, $this->model->insertProductData($productMock, []));
    }
}
