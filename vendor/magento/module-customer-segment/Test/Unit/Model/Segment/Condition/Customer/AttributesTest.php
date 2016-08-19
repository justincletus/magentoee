<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSegment\Test\Unit\Model\Segment\Condition\Customer;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer as ResourceCustomer;
use Magento\CustomerSegment\Model\ResourceModel\Segment as ResourceSegment;
use Magento\CustomerSegment\Model\Segment\Condition\Customer\Attributes;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\LayoutInterface;
use Magento\Rule\Model\Condition\Context;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AttributesTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Attributes */
    protected $model;

    /** @var string  */
    protected $attributeBackendTable = 'backend_table';

    /** @var string  */
    protected $attributeCode = 'default_billing';

    /** @var int  */
    protected $attributeId = 1;

    /** @var string  */
    protected $attributeFrontendLabel = 'frontend_label';

    /** @var  Context |\PHPUnit_Framework_MockObject_MockObject */
    protected $context;

    /** @var  ResourceSegment |\PHPUnit_Framework_MockObject_MockObject */
    protected $resourceSegment;

    /** @var  ResourceCustomer |\PHPUnit_Framework_MockObject_MockObject */
    protected $resourceCustomer;

    /** @var  EavConfig |\PHPUnit_Framework_MockObject_MockObject */
    protected $eavConfig;

    /** @var  AssetRepository |\PHPUnit_Framework_MockObject_MockObject */
    protected $assetRepository;

    /** @var  TimezoneInterface |\PHPUnit_Framework_MockObject_MockObject */
    protected $localeDate;

    /** @var  LayoutInterface |\PHPUnit_Framework_MockObject_MockObject */
    protected $layout;

    /** @var  Attribute |\PHPUnit_Framework_MockObject_MockObject */
    protected $attribute;

    /** @var  Customer |\PHPUnit_Framework_MockObject_MockObject */
    protected $customer;

    /** @var  Select |\PHPUnit_Framework_MockObject_MockObject */
    protected $select;

    /** @var  AdapterInterface |\PHPUnit_Framework_MockObject_MockObject */
    protected $connectionMock;

    protected function setUp()
    {
        $this->prepareContext();
        $this->prepareResourceSegment();
        $this->prepareResourceCustomer();

        $this->eavConfig = $this->getMockBuilder('Magento\Eav\Model\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->customer = $this->getMockBuilder('Magento\Customer\Model\Customer')
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = new Attributes(
            $this->context,
            $this->resourceSegment,
            $this->resourceCustomer,
            $this->eavConfig
        );
    }

    protected function prepareContext()
    {
        $this->assetRepository = $this->getMockBuilder('Magento\Framework\View\Asset\Repository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->localeDate = $this->getMockBuilder('Magento\Framework\Stdlib\DateTime\TimezoneInterface')
            ->getMockForAbstractClass();

        $this->layout = $this->getMockBuilder('Magento\Framework\View\LayoutInterface')
            ->getMockForAbstractClass();

        $this->context = $this->getMockBuilder('Magento\Rule\Model\Condition\Context')
            ->disableOriginalConstructor()
            ->setMethods([
                'getAssetRepository',
                'getLocaleDate',
                'getLayout',
            ])
            ->getMock();
        $this->context->expects($this->any())
            ->method('getAssetRepository')
            ->willReturn($this->assetRepository);
        $this->context->expects($this->any())
            ->method('getLocaleDate')
            ->willReturn($this->localeDate);
        $this->context->expects($this->any())
            ->method('getLayout')
            ->willReturn($this->layout);
    }

    protected function prepareResourceSegment()
    {
        $this->select = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->disableOriginalConstructor()
            ->setMethods([
                'from',
                'where',
                'limit',
                'reset',
                'columns',
            ])
            ->getMock();

        $this->connectionMock = $this->getMockBuilder('Magento\Framework\DB\Adapter\AdapterInterface')
            ->getMockForAbstractClass();

        $this->resourceSegment = $this->getMockBuilder('Magento\CustomerSegment\Model\ResourceModel\Segment')
            ->disableOriginalConstructor()
            ->getMock();

        $this->resourceSegment->expects($this->any())
            ->method('createSelect')
            ->willReturn($this->select);

        $this->resourceSegment->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->connectionMock);
    }

    protected function prepareResourceCustomer()
    {
        $attributeFrontendInput = 'frontend_input';
        $isUsedForCustomerSegment = true;

        $this->attribute = $this->getMockBuilder('Magento\Eav\Model\Entity\Attribute')
            ->disableOriginalConstructor()
            ->setMethods([
                'getFrontendLabel',
                'getFrontendInput',
                'getIsUsedForCustomerSegment',
                'getAttributeCode',
                'getBackendTable',
                'isStatic',
                'getId',
            ])
            ->getMock();

        $this->attribute->expects($this->any())
            ->method('getFrontendLabel')
            ->willReturn($this->attributeFrontendLabel);
        $this->attribute->expects($this->any())
            ->method('getFrontendInput')
            ->willReturn($attributeFrontendInput);
        $this->attribute->expects($this->any())
            ->method('getIsUsedForCustomerSegment')
            ->willReturn($isUsedForCustomerSegment);
        $this->attribute->expects($this->any())
            ->method('getAttributeCode')
            ->willReturn($this->attributeCode);
        $this->attribute->expects($this->any())
            ->method('getBackendTable')
            ->willReturn($this->attributeBackendTable);
        $this->attribute->expects($this->any())
            ->method('getId')
            ->willReturn($this->attributeId);

        $this->resourceCustomer = $this->getMockBuilder('Magento\Customer\Model\ResourceModel\Customer')
            ->disableOriginalConstructor()
            ->getMock();

        $this->resourceCustomer->expects($this->any())
            ->method('loadAllAttributes')
            ->willReturnSelf();
        $this->resourceCustomer->expects($this->any())
            ->method('getAttributesByCode')
            ->willReturn([$this->attribute]);
    }

    public function testGetMatchedEvents()
    {
        $this->assertEquals(['customer_save_commit_after'], $this->model->getMatchedEvents());
    }

    public function testGetNewChildSelectOptions()
    {
        $expected = [
            [
                'value' => get_class($this->model) . '|' . $this->attributeCode,
                'label' => $this->attributeFrontendLabel,
            ],
        ];

        $this->assertEquals($expected, $this->model->getNewChildSelectOptions());
    }

    public function testGetAttributeObject()
    {
        $this->eavConfig->expects($this->once())
            ->method('getAttribute')
            ->with('customer', $this->attribute)
            ->willReturn($this->attribute);

        $this->model->setData('attribute', $this->attribute);

        $this->assertEquals($this->attribute, $this->model->getAttributeObject());
    }

    /**
     * @param bool $useCustomer
     * @param int $websiteId
     * @param bool $isFiltered
     * @param string $expressionString
     * @param bool $isStatic
     * @param string $ruleValue
     * @param bool $result
     * @dataProvider dataProviderGetConditionsSql
     */
    public function testGetConditionsSql(
        $useCustomer,
        $websiteId,
        $isFiltered,
        $expressionString,
        $isStatic,
        $ruleValue,
        $result
    ) {
        $expression = new \Zend_Db_Expr($expressionString, '1', '0');

        $this->select->expects($this->any())
            ->method('from')
            ->willReturnMap([
                [['' => new \Zend_Db_Expr('dual')], [new \Zend_Db_Expr(0)], null, $this->select],
                [['main' => $this->attributeBackendTable], [new \Zend_Db_Expr(1)], null, $this->select],
            ]);
        $this->select->expects($this->any())
            ->method('where')
            ->willReturnMap([
                ['main.entity_id = :customer_id', null, null, $this->select],
                ['main.' . $this->attributeCode . ' IS NOT NULL', null, null, $this->select],
                ['main.attribute_id = ?.' . $this->attributeCode, null, null, $this->select],
            ]);
        $this->select->expects($this->any())
            ->method('limit')
            ->willReturnMap([
                [1, null, $this->select],
            ]);
        $this->select->expects($this->any())
            ->method('reset')
            ->willReturnMap([
                [\Magento\Framework\DB\Select::COLUMNS, $this->select],
            ]);
        $this->select->expects($this->any())
            ->method('columns')
            ->willReturnMap([
                [new \Zend_Db_Expr($expression), null, $this->select],
            ]);

        $this->eavConfig->expects($this->any())
            ->method('getAttribute')
            ->with('customer', $this->attribute)
            ->willReturn($this->attribute);

        $this->connectionMock->expects($this->any())
            ->method('getCheckSql')
            ->with($expressionString, '1', '0')
            ->willReturn($expression);

        $this->attribute->expects($this->any())
            ->method('isStatic')
            ->willReturn($isStatic);

        $customer = $useCustomer ? $this->customer : null;
        $this->model->setData('attribute', $this->attribute);
        $this->model->setData('value', $ruleValue);

        $expected = $result ? $this->select : null;
        $this->assertEquals($expected, $this->model->getConditionsSql($customer, $websiteId, $isFiltered));
    }

    /**
     * 1. Use Customer flag
     * 2. Website ID
     * 3. Is Filtered flag
     * 4. Expression string
     * 5. Is Static flag
     * 6. Rule value
     * 7. RESULT flag
     *
     * @return array
     */
    public function dataProviderGetConditionsSql()
    {
        return [
            [false, 1, true, null, false, null, false],
            [true, 1, true, 'COUNT(*) = 0', true, null, true],
            [true, 1, true, 'COUNT(*) != 0', false, 'is_exists', true],
        ];
    }
}
