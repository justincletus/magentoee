<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\GiftRegistry\Model\Entity
 */
namespace Magento\GiftRegistry\Test\Unit\Model;

class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * GiftRegistry instance
     *
     * @var \Magento\GiftRegistry\Model\Entity
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_store;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_transportBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemModelMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockRegistryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItemMock;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressDataFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp()
    {
        $resource = $this->getMock('Magento\GiftRegistry\Model\ResourceModel\Entity', [], [], '', false);

        $this->_store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $this->_storeManagerMock = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->setMethods(['getStore'])
            ->getMockForAbstractClass();
        $this->_storeManagerMock->expects($this->any())->method('getStore')->will($this->returnValue($this->_store));

        $this->_transportBuilderMock = $this->getMock(
            '\Magento\Framework\Mail\Template\TransportBuilder',
            [],
            [],
            '',
            false
        );

        $this->_transportBuilderMock->expects($this->any())->method('setTemplateOptions')->will($this->returnSelf());
        $this->_transportBuilderMock->expects($this->any())->method('setTemplateVars')->will($this->returnSelf());
        $this->_transportBuilderMock->expects($this->any())->method('addTo')->will($this->returnSelf());
        $this->_transportBuilderMock->expects($this->any())->method('setFrom')->will($this->returnSelf());
        $this->_transportBuilderMock->expects($this->any())->method('setTemplateIdentifier')->will($this->returnSelf());
        $this->_transportBuilderMock->expects($this->any())->method('getTransport')
            ->will($this->returnValue($this->getMock('Magento\Framework\Mail\TransportInterface')));

        $this->_store->expects($this->any())->method('getId')->will($this->returnValue(1));

        $appState = $this->getMock('Magento\Framework\App\State', [], [], '', false);

        $eventDispatcher = $this->getMock(
            'Magento\Framework\Event\ManagerInterface',
            [],
            [],
            '',
            false,
            false
        );
        $cacheManager = $this->getMock('Magento\Framework\App\CacheInterface', [], [], '', false, false);
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $actionValidatorMock = $this->getMock(
            '\Magento\Framework\Model\ActionValidator\RemoveAction',
            [],
            [],
            '',
            false
        );
        $context = new \Magento\Framework\Model\Context(
            $logger,
            $eventDispatcher,
            $cacheManager,
            $appState,
            $actionValidatorMock
        );
        $giftRegistryData = $this->getMock(
            'Magento\GiftRegistry\Helper\Data',
            ['getRegistryLink'],
            [],
            '',
            false,
            false
        );
        $giftRegistryData->expects($this->any())->method('getRegistryLink')->will($this->returnArgument(0));
        $coreRegistry = $this->getMock('Magento\Framework\Registry', [], [], '', false);

        $attributeConfig = $this->getMock('Magento\GiftRegistry\Model\Attribute\Config', [], [], '', false);
        $this->itemModelMock = $this->getMock('Magento\GiftRegistry\Model\Item', [], [], '', false);
        $type = $this->getMock('Magento\GiftRegistry\Model\Type', [], [], '', false);
        $this->stockRegistryMock = $this->getMock(
            'Magento\CatalogInventory\Model\StockRegistry',
            [],
            [],
            '',
            false
        );
        $this->stockItemMock = $this->getMock(
            '\Magento\CatalogInventory\Model\Stock\StockItemRepository',
            ['getIsQtyDecimal'],
            [],
            '',
            false
        );
        $session = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);

        $this->addressDataFactory = $this->getMock(
            'Magento\Customer\Api\Data\AddressInterfaceFactory',
            ['create'],
            [],
            '',
            false
        );
        $quoteRepository = $this->getMock('\Magento\Quote\Api\CartRepositoryInterface');
        $customerFactory = $this->getMock('Magento\Customer\Model\CustomerFactory', [], [], '', false);
        $personFactory = $this->getMock('Magento\GiftRegistry\Model\PersonFactory', [], [], '', false);
        $this->itemFactoryMock = $this->getMock('Magento\GiftRegistry\Model\ItemFactory', ['create'], [], '', false);
        $addressFactory = $this->getMock('Magento\Customer\Model\AddressFactory', [], [], '', false);
        $productRepository = $this->getMock('Magento\Catalog\Model\ProductRepository', [], [], '', false);
        $dateFactory = $this->getMock('Magento\Framework\Stdlib\DateTime\DateTimeFactory', [], [], '', false);
        $escaper = $this->getMock('Magento\Framework\Escaper', ['escapeHtml'], [], '', false, false);
        $escaper->expects($this->any())->method('escapeHtml')->will($this->returnArgument(0));
        $mathRandom = $this->getMock('Magento\Framework\Math\Random', [], [], '', false, false);
        $this->scopeConfigMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface', [], [], '', false);
        $quoteFactory = $this->getMock('\Magento\Quote\Model\QuoteFactory', [], [], '', false);
        $inlineTranslate = $this->getMock(
            '\Magento\Framework\Translate\Inline\StateInterface',
            [],
            [],
            '',
            false,
            false
        );

        $this->customerRepositoryMock = $this->getMock(
            '\Magento\Customer\Api\CustomerRepositoryInterface',
            [],
            [],
            '',
            false
        );
        $this->_model = new \Magento\GiftRegistry\Model\Entity(
            $context,
            $coreRegistry,
            $giftRegistryData,
            $this->_storeManagerMock,
            $this->_transportBuilderMock,
            $type,
            $attributeConfig,
            $this->itemModelMock,
            $this->stockRegistryMock,
            $session,
            $quoteRepository,
            $customerFactory,
            $personFactory,
            $this->itemFactoryMock,
            $addressFactory,
            $this->addressDataFactory,
            $productRepository,
            $dateFactory,
            $escaper,
            $mathRandom,
            $this->scopeConfigMock,
            $inlineTranslate,
            $quoteFactory,
            $this->customerRepositoryMock,
            $resource,
            null,
            []
        );
    }

    /**
     * @param array $arguments
     * @param array $expectedResult
     * @dataProvider invalidSenderAndRecipientInfoDataProvider
     */
    public function testSendShareRegistryEmailsWithInvalidSenderAndRecipientInfoReturnsError(
        $arguments,
        $expectedResult
    ) {
        $senderEmail = 'someuser@magento.com';
        $maxRecipients = 3;
        $customerMock = $this->getMock('\Magento\Customer\Api\Data\CustomerInterface', [], [], '', false);
        $this->customerRepositoryMock->expects($this->once())->method('getById')->willReturn($customerMock);
        $customerMock->expects($this->once())->method('getEmail')->willReturn($senderEmail);

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->willReturn($maxRecipients);
        $this->_initSenderInfo($arguments['sender_name'], $arguments['sender_message'], $senderEmail);
        $this->_model->setRecipients($arguments['recipients']);
        $result = $this->_model->sendShareRegistryEmails();

        $this->assertEquals($expectedResult['success'], $result->getIsSuccess());
        $this->assertEquals($expectedResult['error_message'], $result->getErrorMessage());
    }

    public function invalidSenderAndRecipientInfoDataProvider()
    {
        return array_merge($this->_invalidRecipientInfoDataProvider(), $this->_invalidSenderInfoDataProvider());
    }

    /**
     * Retrieve data for invalid sender cases
     *
     * @return array
     */
    protected function _invalidSenderInfoDataProvider()
    {
        return [
            [
                [
                    'sender_name' => null,
                    'sender_message' => 'Hello world',
                    'recipients' => []
                ],
                ['success' => false, 'error_message' => 'You need to enter sender data.']
            ],
            [
                [
                    'sender_name' => 'John Doe',
                    'sender_message' => null,
                    'recipients' => []
                ],
                ['success' => false, 'error_message' => 'You need to enter sender data.']
            ],
            [
                [
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'recipients' => []
                ],
                ['success' => false, 'error_message' => 'Please add invitees.']
            ],
        ];
    }

    /**
     * Retrieve data for invalid recipient cases
     *
     * @return array
     */
    protected function _invalidRecipientInfoDataProvider()
    {
        return [
            [
                [
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'recipients' => [['email' => 'invalid_email']]
                ],
                ['success' => false, 'error_message' => 'Please enter a valid invitee email address.']
            ],
            [
                [
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'recipients' => [['email' => 'john.doe@example.com', 'name' => '']]
                ],
                ['success' => false, 'error_message' => 'Please enter an invitee name.']
            ],
            [
                [
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'recipients' => []
                ],
                ['success' => false, 'error_message' => 'Please add invitees.']
            ]
        ];
    }

    /**
     * Initialize sender info
     *
     * @param string $senderName
     * @param string $senderMessage
     * @param string $senderEmail
     * @return void
     */
    protected function _initSenderInfo($senderName, $senderMessage, $senderEmail)
    {
        $this->_model->setSenderName($senderName)->setSenderMessage($senderMessage)->setSenderEmail($senderEmail);
    }

    public function testUpdateItems()
    {
        $modelId = 1;
        $productId = 1;
        $items = [
            1 => ['note' => 'test', 'qty' => 5],
            2 => ['note' => '', 'qty' => 1, 'delete' => 1]
        ];
        $this->_model->setId($modelId);
        $modelMock = $this->getMock(
            '\Magento\Framework\Model\AbstractModel',
            ['getProductId', 'getId', 'getEntityId', 'save', 'delete', 'isDeleted', 'setQty', 'setNote'],
            [],
            '',
            false
        );
        $this->itemFactoryMock->expects($this->exactly(2))->method('create')->willReturn($this->itemModelMock);
        $this->itemModelMock->expects($this->exactly(4))->method('load')->willReturn($modelMock);
        $modelMock->expects($this->atLeastOnce())->method('getId')->willReturn(1);
        $modelMock->expects($this->atLeastOnce())->method('getEntityId')->willReturn(1);
        $modelMock->expects($this->once())->method('getProductId')->willReturn($productId);
        $modelMock->expects($this->once())->method('delete');
        $modelMock->expects($this->once())->method('setQty')->with($items[1]['qty']);
        $modelMock->expects($this->once())->method('setNote')->with($items[1]['note']);
        $modelMock->expects($this->once())->method('save');
        $this->stockRegistryMock->expects($this->once())->method('getStockItem')->with($productId)
            ->willReturn($this->stockItemMock);
        $this->stockItemMock->expects($this->once())->method('getIsQtyDecimal')->willReturn(10);
        $this->assertEquals($this->_model, $this->_model->updateItems($items));
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Please correct the  gift registry item quantity.
     */
    public function testUpdateItemsWithIncorrectQuantity()
    {
        $modelId = 1;
        $productId = 1;
        $items = [
            1 => ['note' => 'test', 'qty' => '.1']
        ];
        $this->_model->setId($modelId);
        $modelMock = $this->getMock(
            '\Magento\Framework\Model\AbstractModel',
            ['getProductId', 'getId', 'getEntityId'],
            [],
            '',
            false
        );
        $this->itemModelMock->expects($this->once())->method('load')->willReturn($modelMock);
        $modelMock->expects($this->atLeastOnce())->method('getId')->willReturn(1);
        $modelMock->expects($this->atLeastOnce())->method('getEntityId')->willReturn(1);
        $modelMock->expects($this->once())->method('getProductId')->willReturn($productId);
        $this->stockRegistryMock->expects($this->once())->method('getStockItem')->with($productId)
            ->willReturn($this->stockItemMock);
        $this->stockItemMock->expects($this->once())->method('getIsQtyDecimal')->willReturn(0);
        $this->assertEquals($this->_model, $this->_model->updateItems($items));
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Please correct the gift registry item ID.
     */
    public function testUpdateItemsWithIncorrectItemId()
    {
        $modelId = 1;
        $items = [
            1 => ['note' => 'test', 'qty' => '.1']
        ];
        $this->_model->setId($modelId);
        $modelMock = $this->getMock(
            '\Magento\Framework\Model\AbstractModel',
            [],
            [],
            '',
            false
        );
        $this->itemModelMock->expects($this->once())->method('load')->willReturn($modelMock);
        $this->assertEquals($this->_model, $this->_model->updateItems($items));
    }

    /**
     * @return array
     */
    public function addressDataProvider()
    {
        return [
            'withoutData' => [null],
            'withData'    => [
                ['street' => 'Baker Street'],
            ]
        ];
    }

    /**
     * @test
     * @dataProvider addressDataProvider
     * @param [] $data
     * @param [] $constructorData
     */
    public function testExportAddressData($data)
    {
        $this->_model->setData('shipping_address', serialize($data));
        $this->addressDataFactory->expects($this->once())
            ->method('create')
            ->willReturn(
                $this->getMockBuilder('Magento\Customer\Model\Data\Address')
                    ->disableOriginalConstructor()
                    ->getMock()
            );

        $this->assertInstanceOf('Magento\Customer\Model\Data\Address', $this->_model->exportAddressData());
    }
}
