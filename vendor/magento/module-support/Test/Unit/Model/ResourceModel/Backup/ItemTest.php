<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\ResourceModel\Backup;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Support\Model\ResourceModel\Backup\Item
     */
    protected $item;

    /**
     * @var \Magento\Framework\App\ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $connection;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->connection = $this->getMock('Magento\Framework\DB\Adapter\AdapterInterface');
        $this->resource = $this->getMock('Magento\Framework\App\ResourceConnection', [], [], '', false);
        $this->resource->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->connection);

        /** @var \Magento\Framework\Model\ResourceModel\Db\Context $context */
        $context = $this->objectManagerHelper->getObject(
            'Magento\Framework\Model\ResourceModel\Db\Context',
            ['resource' => $this->resource]
        );

        $this->item = $this->objectManagerHelper->getObject(
            'Magento\Support\Model\ResourceModel\Backup\Item',
            ['context' => $context]
        );
    }

    /**
     * @return void
     */
    public function testLoadItemByBackupIdAndType()
    {
        $backupId = 1;
        $type = 2;

        /** @var \Magento\Framework\DB\Select|\PHPUnit_Framework_MockObject_MockObject $select */
        $select = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);
        $select->expects($this->once())
            ->method('from')
            ->willReturnSelf();
        $select->expects($this->any())
            ->method('where')
            ->willReturnMap([
                ['backup_id = ?', $backupId, null, $select],
                ['type = ?', $type, null, $select],
            ]);

        $collectionData = ['someKey' => 'someValue'];
        $this->connection->expects($this->once())
            ->method('select')
            ->willReturn($select);
        $this->connection->expects($this->once())
            ->method('fetchRow')
            ->with($select)
            ->willReturn($collectionData);

        /** @var \Magento\Support\Model\Backup\AbstractItem|\PHPUnit_Framework_MockObject_MockObject $abstractItem */
        $abstractItem = $this->getMockBuilder('Magento\Support\Model\Backup\AbstractItem')
            ->setMethods(['addData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractItem->expects($this->once())
            ->method('addData')
            ->with($collectionData)
            ->willReturnSelf();

        $this->assertEquals($this->item, $this->item->loadItemByBackupIdAndType($abstractItem, $backupId, $type));
    }
}
