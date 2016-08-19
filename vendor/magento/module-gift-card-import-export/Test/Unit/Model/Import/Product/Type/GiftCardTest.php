<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardImportExport\Test\Unit\Model\Import\Product\Type;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * Class GiftCardTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GiftCardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager|\Magento\GiftCardImportExport\Model\Import\Product\Type\GiftCard
     */
    protected $giftcardModelMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrSetColFacMock;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrSetColMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $prodAttrColFacMock;

    /**
     * @var \Magento\Framework\App\ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Framework\DB\Select|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $select;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityModelMock;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Product\StoreResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeResolverMock;

    /**
     * @var \Magento\Framework\DB\Adapter\Pdo\Mysql|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeMock;

    /**
     * @var \Magento\Config\Model\Config\Source\Email\TemplateFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateFactory;

    /**
     * @var \Magento\Config\Model\Config\Source\Email\Template|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $template;

    /**
     * @var \Magento\Framework\Phrase|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $phrase;

    /**
     * Set up
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->connectionMock = $this->getMock(
            'Magento\Framework\DB\Adapter\Pdo\Mysql',
            ['select', 'fetchAll', 'fetchPairs', 'joinLeft', 'insertOnDuplicate', 'delete', 'quoteInto', 'fetchAssoc'],
            [],
            '',
            false
        );
        $this->select = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);
        $this->select->expects($this->any())->method('from')->will($this->returnSelf());
        $this->select->expects($this->any())->method('where')->will($this->returnSelf());
        $this->select->expects($this->any())->method('joinLeft')->will($this->returnSelf());
        $adapter = $this->getMock('Magento\Framework\DB\Adapter\Pdo\Mysql', [], [], '', false);
        $adapter->expects($this->any())->method('quoteInto')->will($this->returnValue('query'));
        $this->select->expects($this->any())->method('getAdapter')->willReturn($adapter);
        $this->connectionMock->expects($this->any())->method('select')->will($this->returnValue($this->select));
        $this->connectionMock->expects($this->any())->method('fetchAll')->will(
            $this->returnValue(
                [
                    [
                        'attribute_set_name' => '123',
                        'attribute_id' => 'giftcard_amounts',
                    ]
                ]
            )
        );
        $this->connectionMock->expects($this->any())->method('insertOnDuplicate')->willReturnSelf();
        $this->connectionMock->expects($this->any())->method('delete')->willReturnSelf();
        $this->connectionMock->expects($this->any())->method('quoteInto')->willReturn('');
        $this->attrSetColFacMock = $this->getMock(
            'Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->attrSetColMock = $this->getMock(
            'Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection',
            ['setEntityTypeFilter'],
            [],
            '',
            false
        );
        $this->attrSetColMock
            ->expects($this->any())
            ->method('setEntityTypeFilter')
            ->will($this->returnValue([]));
        $this->prodAttrColFacMock = $this->getMock(
            'Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $attrCollection = $this->getMock(
            'Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection',
            [],
            [],
            '',
            false
        );
        $this->attributeMock = $this->getMock(
            'Magento\Catalog\Model\ResourceModel\Eav\Attribute',
            ['getId', 'getIsVisible', 'getAttributeCode'],
            [],
            '',
            false
        );
        $this->attributeMock->expects($this->any())->method('getAttributeCode')->willReturn('giftcard_amounts');
        $this->attributeMock->expects($this->any())->method('getIsVisible')->willReturn(true);
        $attrCollection->expects($this->any())->method('addFieldToFilter')->willReturn([$this->attributeMock]);
        $this->prodAttrColFacMock->expects($this->any())->method('create')->will($this->returnValue($attrCollection));
        $this->resourceMock = $this->getMock(
            'Magento\Framework\App\ResourceConnection',
            ['getConnection', 'getTableName'],
            [],
            '',
            false
        );
        $this->resourceMock->expects($this->any())->method('getConnection')->will(
            $this->returnValue($this->connectionMock)
        );
        $this->resourceMock->expects($this->any())->method('getTableName')->will(
            $this->returnValue('tableName')
        );
        $this->entityModelMock = $this->getMock(
            'Magento\CatalogImportExport\Model\Import\Product',
            [
                'addMessageTemplate',
                'getEntityTypeId',
                'getBehavior',
                'getNewSku',
                'getNextBunch',
                'isRowAllowedToImport',
                'getParameters',
                'addRowError',
                'getRowScope'
            ],
            [],
            '',
            false
        );
        $this->entityModelMock->expects($this->any())->method('addMessageTemplate')->will($this->returnSelf());
        $this->entityModelMock->expects($this->any())->method('getEntityTypeId')->will($this->returnValue(5));
        $this->entityModelMock->expects($this->any())->method('getParameters')->will($this->returnValue([]));
        $this->entityModelMock->expects($this->any())->method('getRowScope')->will($this->returnValue(-1));
        $this->storeResolverMock = $this->getMock(
            'Magento\CatalogImportExport\Model\Import\Product\StoreResolver',
            [],
            [],
            '',
            false
        );
        $this->phrase = $this->getMock(
            'Magento\Framework\Phrase',
            ['render'],
            [],
            '',
            false
        );
        $this->phrase->expects($this->any())->method('render')->will($this->returnValue('Template name'));
        $this->template = $this->getMock(
            '\Magento\Config\Model\Config\Source\Email\TemplateFactory',
            ['setPath', 'toOptionArray'],
            [],
            '',
            false
        );
        $this->template->expects($this->any())->method('setPath')->willReturnSelf();
        $this->template->expects($this->any())
            ->method('toOptionArray')
            ->will($this->returnValue(
                [[
                    'value' => '1',
                    'label' => $this->phrase
                ]]
            ));
        $this->templateFactory = $this->getMock(
            '\Magento\Config\Model\Config\Source\Email\TemplateFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->templateFactory->expects($this->any())->method('create')->will($this->returnValue($this->template));
        $this->giftcardModelMock = $objectManager->getObject(
            'Magento\GiftCardImportExport\Model\Import\Product\Type\GiftCard',
            [
                'attrSetColFac' => $this->attrSetColFacMock,
                'prodAttrColFac' => $this->prodAttrColFacMock,
                'resource' => $this->resourceMock,
                'params' => [
                    $this->entityModelMock,
                    'giftcard'
                ],
                'storeResolver' => $this->storeResolverMock,
                'templateFactory' => $this->templateFactory
            ]
        );
    }

    /**
     * Test saveData()
     *
     * @param array $bunch
     * @param array $sku
     * @param array $allowed
     * @dataProvider saveDataProvider
     */
    public function testSaveData($bunch, $sku, $allowed)
    {
        $this->entityModelMock->expects($this->at(0))->method('getNextBunch')->willReturn($bunch);
        $this->entityModelMock->expects($this->any())->method('getNewSku')->willReturn($sku);
        $this->entityModelMock->expects($this->any())->method('isRowAllowedToImport')->willReturn($allowed);
        $this->attributeMock->expects($this->any())->method('getId')->willReturn(123);
        $this->assertInstanceOf(
            'Magento\GiftCardImportExport\Model\Import\Product\Type\GiftCard',
            $this->giftcardModelMock->saveData()
        );
    }

    /**
     * Test isRowValid()
     *
     * @param int|null $attributeId
     * @param string   $amount
     * @param bool     $result
     * @dataProvider isValidDataProvider
     */
    public function testIsRowValid($attributeId, $amount, $result)
    {
        $rowData = [
            'sku' => 'giftcardsku',
            'attribute_set_code' => 'Default',
            'product_type' => 'giftcard',
            'name' => 'giftcard',
            'giftcard_type' => 'virtual',
            'giftcard_amount' => $amount,
            'giftcard_allow_open_amount' => '',
            'giftcard_open_amount_min' => '',
            'giftcard_open_amount_max' => '',
            'giftcard_is_redeemable' => '0',
            'giftcard_lifetime' => '1',
            'giftcard_allow_message' => '1',
            'giftcard_email_template' => 'Default',
        ];
        $this->attributeMock->expects($this->any())->method('getId')->willReturn($attributeId);
        $this->assertEquals($result, $this->giftcardModelMock->isRowValid($rowData, 1));
    }

    /**
     * Test prepareAttributesWithDefaultValueForSave()
     */
    public function testPrepareAttributesWithDefaultValueForSave()
    {
        $rowData = [
            'sku' => 'giftcardsku',
            'attribute_set_code' => 'Default',
            'product_type' => 'giftcard',
            '_attribute_set' => '123',
            'name' => 'giftcard',
            'giftcard_type' => 'virtual',
            'giftcard_amount' => '123',
            'giftcard_allow_open_amount' => '',
            'giftcard_open_amount_min' => '',
            'giftcard_open_amount_max' => '',
            'giftcard_is_redeemable' => '0',
            'giftcard_lifetime' => '1',
            'giftcard_allow_message' => '1',
            'giftcard_email_template' => 'Default',
        ];
        $resultAttributes = $this->giftcardModelMock->prepareAttributesWithDefaultValueForSave($rowData);
        $this->assertNull($resultAttributes['weight']);
        $this->assertArrayNotHasKey('giftcard_allow_open_amount', $resultAttributes);
        $this->assertArrayHasKey('allow_open_amount', $resultAttributes);
    }

    /**
     * Dataprovider for testSaveData()
     *
     * @return array
     */
    public function saveDataProvider()
    {
        return [
            [
                'bunch' => [
                    [
                        'sku' => 'giftcardsku1',
                        'product_type' => 'giftcard',
                        'name' => 'giftcard',
                        'giftcard_type' => 'virtual',
                        'giftcard_amount' => '100, 200',
                        'giftcard_allow_open_amount' => '',
                        'giftcard_open_amount_min' => '',
                        'giftcard_open_amount_max' => '',
                        'giftcard_is_redeemable' => '0',
                        'giftcard_lifetime' => '1',
                        'giftcard_allow_message' => '1',
                        'giftcard_email_template' => 'Default',
                    ],
                    [
                        'sku' => 'giftcardsku2',
                        'product_type' => 'giftcard',
                        'name' => 'giftcard',
                        'giftcard_type' => 'physical',
                        'giftcard_amount' => '',
                        'giftcard_allow_open_amount' => '1',
                        'giftcard_open_amount_min' => '100',
                        'giftcard_open_amount_max' => '200',
                        'giftcard_is_redeemable' => '1',
                        'giftcard_lifetime' => '6',
                        'giftcard_allow_message' => '0',
                        'giftcard_email_template' => 'Default',
                    ],
                    [
                        'sku' => 'giftcardsku3',
                        'product_type' => 'giftcard',
                        'name' => 'giftcard',
                        'giftcard_type' => 'combined',
                        'giftcard_amount' => '100, 200',
                        'giftcard_allow_open_amount' => '',
                        'giftcard_open_amount_min' => '',
                        'giftcard_open_amount_max' => '',
                        'giftcard_is_redeemable' => '',
                        'giftcard_lifetime' => '6',
                        'giftcard_allow_message' => '',
                        'giftcard_email_template' => 'Default',
                    ],
                ],
                'sku' => [
                    'giftcardsku1' => [
                        'entity_id' => '1',
                        'type_id' => 'giftcard',
                        'attr_set_id' => '4',
                        'attr_set_code' => 'Default',
                    ],
                    'giftcardsku2' => [
                        'entity_id' => '1',
                        'type_id' => 'giftcard',
                        'attr_set_id' => '4',
                        'attr_set_code' => 'Default',
                    ],
                    'giftcardsku3' => [
                        'entity_id' => '1',
                        'type_id' => 'giftcard',
                        'attr_set_id' => '4',
                        'attr_set_code' => 'Default',
                    ],
                ],
                'allowed' => true,
            ],
            [
                'bunch' => [
                    [
                        'sku' => 'giftcardsku1',
                        'product_type' => 'giftcard',
                        'name' => 'giftcard',
                        'giftcard_type' => 'virtual',
                        'giftcard_amount' => '100, 200',
                        'giftcard_allow_open_amount' => '',
                        'giftcard_open_amount_min' => '',
                        'giftcard_open_amount_max' => '',
                        'giftcard_is_redeemable' => '',
                        'giftcard_lifetime' => '',
                        'giftcard_allow_message' => '',
                        'giftcard_email_template' => '',
                    ],
                ],
                'sku' => [
                    'giftcardsku1' => [
                        'entity_id' => '1',
                        'type_id' => 'giftcard',
                        'attr_set_id' => '4',
                        'attr_set_code' => 'Default',
                    ],
                ],
                'allowed' => false,
            ],
        ];
    }

    /**
     * Dataprovider for testIsValid
     *
     * @return array
     */
    public function isValidDataProvider()
    {
        return [
            [
                'attributeId' => 123,
                'amount' => '',
                'result' => false,
            ],
            [
                'attributeId' => null,
                'amount' => '100',
                'result' => false,
            ],
        ];
    }
}
