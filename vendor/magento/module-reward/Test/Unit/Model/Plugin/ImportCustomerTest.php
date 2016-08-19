<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reward\Test\Unit\Model\Plugin;

use Magento\Catalog\Model\Product;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class ImportCustomerTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Magento\Reward\Model\Plugin\ImportCustomer */
    private $plugin;

    /** @var  MockObject|\Magento\CustomerImportExport\Model\Import\Customer */
    private $importCustomer;

    public function setUp()
    {
        $this->plugin = new \Magento\Reward\Model\Plugin\ImportCustomer();
        $this->importCustomer = $this->getMockBuilder('Magento\CustomerImportExport\Model\Import\Customer')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testAfterGetIdentities()
    {
        $previousColumns  = [
            'column_name_1',
            'column_name_2',
        ];
        $columnNames = $this->plugin->afterGetValidColumnNames($this->importCustomer, $previousColumns);
        $this->assertCount(4, $columnNames);
    }
}
