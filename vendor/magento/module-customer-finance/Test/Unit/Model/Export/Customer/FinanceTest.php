<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerFinance\Test\Unit\Model\Export\Customer;

use Magento\CustomerFinance\Model\Export\Customer\Finance;

class FinanceTest extends \PHPUnit_Framework_TestCase
{
    /**#@+
     * Test attribute code and website specific attribute code
     */
    const ATTRIBUTE_CODE = 'code1';

    const WEBSITE_ATTRIBUTE_CODE = 'website1_code1';

    /**#@-*/

    /**
     * Websites array (website id => code)
     *
     * @var array
     */
    protected $_websites = [\Magento\Store\Model\Store::DEFAULT_STORE_ID => 'admin', 1 => 'website1'];

    /**
     * Attributes array
     *
     * @var array
     */
    protected $_attributes = [['attribute_id' => 1, 'attribute_code' => self::ATTRIBUTE_CODE]];

    /**
     * Customer data
     *
     * @var array
     */
    protected $_customerData = [
        'website_id' => 1,
        'email' => '@email@domain.com',
        self::WEBSITE_ATTRIBUTE_CODE => 1,
    ];

    /**
     * Customer financial data export model
     *
     * @var \Magento\CustomerFinance\Model\Export\Customer\Finance
     */
    protected $_model;

    protected function setUp()
    {
        $scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $customerCollectionFactory = $this->getMock(
            'Magento\CustomerFinance\Model\ResourceModel\Customer\CollectionFactory',
            ['create'],
            [],
            '',
            false,
            false
        );

        $eavCustomerFactory = $this->getMock(
            'Magento\CustomerImportExport\Model\Export\CustomerFactory',
            ['create'],
            [],
            '',
            false,
            false
        );

        $storeManager = $this->getMock('Magento\Store\Model\StoreManager', [], [], '', false);
        $storeManager->expects(
            $this->exactly(2)
        )->method(
            'getWebsites'
        )->will(
            $this->returnCallback([$this, 'getWebsites'])
        );

        $this->_model = new \Magento\CustomerFinance\Model\Export\Customer\Finance(
            $scopeConfig,
            $storeManager,
            $this->getMock('Magento\ImportExport\Model\Export\Factory', ['create'], [], '', false, false),
            $this->getMock(
                'Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory',
                ['create'],
                [],
                '',
                false,
                false
            ),
            $customerCollectionFactory,
            $eavCustomerFactory,
            $this->getMock('Magento\CustomerFinance\Helper\Data', [], [], '', false, false),
            $this->_getModelDependencies()
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Create mocks for all $this->_model dependencies
     *
     * @return array
     */
    protected function _getModelDependencies()
    {
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $translator = $this->getMock('stdClass');

        /** @var $attributeCollection \Magento\Framework\Data\Collection|\PHPUnit_Framework_TestCase */
        $attributeCollection = $this->getMock(
            'Magento\Framework\Data\Collection',
            ['getEntityTypeCode'],
            [$this->getMock('Magento\Framework\Data\Collection\EntityFactory', [], [], '', false)]
        );
        foreach ($this->_attributes as $attributeData) {
            $arguments = $objectManagerHelper->getConstructArguments(
                'Magento\Eav\Model\Entity\Attribute\AbstractAttribute'
            );
            $arguments['data'] = $attributeData;
            $attribute = $this->getMockBuilder(
                'Magento\Eav\Model\Entity\Attribute\AbstractAttribute'
            )->setConstructorArgs(
                $arguments
            )->setMethods(
                ['_construct']
            )->getMock();
            $attributeCollection->addItem($attribute);
        }

        $data = [
            'translator' => $translator,
            'attribute_collection' => $attributeCollection,
            'page_size' => 1,
            'collection_by_pages_iterator' => 'not_used',
            'entity_type_id' => 1,
            'customer_collection' => 'not_used',
            'customer_entity' => 'not_used',
            'module_helper' => 'not_used',
        ];

        return $data;
    }

    /**
     * Get websites stub
     *
     * @param bool $withDefault
     * @return array
     */
    public function getWebsites($withDefault = false)
    {
        $websites = [];
        if (!$withDefault) {
            unset($websites[0]);
        }
        foreach ($this->_websites as $id => $code) {
            if (!$withDefault && $id == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
                continue;
            }
            $websiteData = ['id' => $id, 'code' => $code];
            $websites[$id] = new \Magento\Framework\DataObject($websiteData);
        }

        return $websites;
    }

    /**
     * Test for method exportItem()
     *
     * @covers \Magento\CustomerFinance\Model\Export\Customer\Finance::exportItem
     */
    public function testExportItem()
    {
        $writer = $this->getMockForAbstractClass(
            'Magento\ImportExport\Model\Export\Adapter\AbstractAdapter',
            [],
            '',
            false,
            false,
            true,
            ['writeRow']
        );

        $writer->expects(
            $this->once()
        )->method(
            'writeRow'
        )->will(
            $this->returnCallback([$this, 'validateWriteRow'])
        );

        $this->_model->setWriter($writer);

        $item = $this->getMockForAbstractClass(
            'Magento\Framework\Model\AbstractModel',
            [],
            '',
            false,
            false,
            true,
            ['__wakeup']
        );
        /** @var $item \Magento\Framework\Model\AbstractModel */
        $item->setData($this->_customerData);

        $this->_model->exportItem($item);
    }

    /**
     * Validate data passed to writer's writeRow() method
     *
     * @param array $row
     */
    public function validateWriteRow(array $row)
    {
        $emailColumn = Finance::COLUMN_EMAIL;
        $this->assertEquals($this->_customerData['email'], $row[$emailColumn]);

        $websiteColumn = Finance::COLUMN_WEBSITE;
        $this->assertEquals($this->_websites[$this->_customerData['website_id']], $row[$websiteColumn]);

        $financeWebsiteCol = Finance::COLUMN_FINANCE_WEBSITE;
        $this->assertEquals($this->_websites[$this->_customerData['website_id']], $row[$financeWebsiteCol]);

        $this->assertEquals($this->_customerData[self::WEBSITE_ATTRIBUTE_CODE], $row[self::ATTRIBUTE_CODE]);
    }
}
