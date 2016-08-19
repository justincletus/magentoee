<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogEvent\Test\Unit\Block\Adminhtml\Catalog\Category\Edit;

class ButtonsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogEvent\Block\Adminhtml\Catalog\Category\Edit\Buttons
     */
    protected $block;

    /**
     * @var \Magento\CatalogEvent\Model\ResourceModel\Event\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactoryMock;

    /**
     * @var \Magento\CatalogEvent\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogEventHelperMock;

    /**
     * @var \Magento\Backend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendDataHelperMock;

    /**
     * @var \Magento\Backend\Block\Template\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\AuthorizationInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $authorizationMock;

    /**
     * @var \Magento\Framework\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    public function setUp()
    {
        $this->contextMock = $this->getMock(
            'Magento\Backend\Block\Template\Context',
            ['getAuthorization', 'getLayout'],
            [],
            '',
            false
        );
        $this->authorizationMock = $this->getMock(
            'Magento\Framework\AuthorizationInterface',
            [],
            [],
            '',
            false
        );
        $this->registryMock = $this->getMock('Magento\Framework\Registry', [], [], '', false);
        $this->contextMock->expects($this->any())
            ->method('getAuthorization')
            ->will($this->returnValue($this->authorizationMock));

        $this->layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);

        $this->contextMock->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($this->layoutMock));

        $this->backendDataHelperMock = $this->getMock('Magento\Backend\Helper\Data', [], [], '', false);
        $this->catalogEventHelperMock = $this->getMock('Magento\CatalogEvent\Helper\Data', [], [], '', false);
        $this->collectionFactoryMock = $this->getMock(
            'Magento\CatalogEvent\Model\ResourceModel\Event\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );


        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->block = $objectManager->getObject(
            'Magento\CatalogEvent\Block\Adminhtml\Catalog\Category\Edit\Buttons',
            [
                'context' => $this->contextMock,
                'backendHelper' => $this->backendDataHelperMock,
                'catalogeventHelper' => $this->catalogEventHelperMock,
                'eventCollectionFactory' => $this->collectionFactoryMock,
                'registry' => $this->registryMock,
            ]
        );
    }

    /**
     * Test get event
     *
     * @return void
     */
    public function testGetEvent()
    {
        $categoryId = 1;
        $this->block->setCategoryId($categoryId);
        $collectionMock = $this->getMock(
            'Magento\CatalogEvent\Model\ResourceModel\Event\Collection',
            ['addFieldToFilter', 'getFirstItem'],
            [],
            '',
            false
        );
        $eventMock = $this->getMock('Magento\CatalogEvent\Model\Event', [], [], '', false);
        $collectionMock->expects($this->once())
            ->method('getFirstItem')
            ->will($this->returnValue($eventMock));

        $collectionMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with('category_id', $categoryId)->will($this->returnSelf());

        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($collectionMock));

        $this->assertEquals($eventMock, $this->block->getEvent());
    }

    /**
     * Test button is never added
     *
     * @param bool $eventsEnabled
     * @param bool $authAllowed
     * @param int|null $categoryId
     * @param int $categoryLevel
     *
     * @dataProvider addButtonsNeverDataProvider
     *
     * @return void
     */
    public function testAddButtonsNever($eventsEnabled, $authAllowed, $categoryId, $categoryLevel)
    {
        $formBlockMock = $this->getMock(
            'Magento\CatalogEvent\Block\Adminhtml\Event\Edit\Form',
            ['addAdditionalButton'],
            [],
            '',
            false
        );

        $parentBlockMock = $this->getMockForAbstractClass(
            'Magento\Framework\View\Element\BlockInterface',
            [],
            '',
            false,
            false,
            true,
            ['getChildBlock']
        );

        $parentBlockMock->expects($this->any())
            ->method('getChildBlock')
            ->with('form')
            ->will($this->returnValue($formBlockMock));

        $this->layoutMock->expects($this->never())
            ->method('getParentName');

        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', ['getId', 'getLevel'], [], '', false);
        $categoryMock->expects($this->any())->method('getId')->will($this->returnValue($categoryId));
        $categoryMock->expects($this->any())->method('getLevel')->will($this->returnValue($categoryLevel));

        $this->registryMock->expects($this->any())
            ->method('registry')
            ->with('category')
            ->will($this->returnValue($categoryMock));

        $this->catalogEventHelperMock->expects($this->any())
            ->method('isEnabled')
            ->will($this->returnValue($eventsEnabled));
        $this->authorizationMock->expects($this->any())
            ->method('isAllowed')
            ->with('Magento_CatalogEvent::events')
            ->will($this->returnValue($authAllowed));

        $eventMock = $this->getMock('Magento\CatalogEvent\Model\Event', [], [], '', false);

        $this->block->setData('event', $eventMock);
        $this->block->addButtons();

    }

    public function addButtonsNeverDataProvider()
    {
        return [
            [true, true, 1, 1],
            [false, true, 1, 2],
            [false, false, 1, 2],
            [true, false, null, 2],
            [true, true, null, null],
            [true, false, 2, 1],
        ];
    }

    /**
     * Testing buttons are added
     *
     * @param int $eventId
     * @param string $expectedButton
     *
     * @dataProvider testAddButtonsDataProvider
     */
    public function testAddButtons($eventId, $expectedButton)
    {
        $formBlockMock = $this->getMock(
            'Magento\CatalogEvent\Block\Adminhtml\Event\Edit\Form',
            ['addAdditionalButton'],
            [],
            '',
            false
        );

        $parentBlockMock = $this->getMockForAbstractClass(
            'Magento\Framework\View\Element\BlockInterface',
            [],
            '',
            false,
            false,
            true,
            ['getChildBlock']
        );

        $parentBlockMock->expects($this->any())
            ->method('getChildBlock')
            ->with('form')
            ->will($this->returnValue($formBlockMock));

        $this->layoutMock->expects($this->any())
            ->method('getParentName')
            ->will($this->returnValue('parent_name'));

        $this->layoutMock->expects($this->atLeastOnce())
            ->method('getBlock')
            ->with('parent_name')
            ->will($this->returnValue($parentBlockMock));

        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', ['getId', 'getLevel'], [], '', false);
        $categoryMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $categoryMock->expects($this->any())->method('getLevel')->will($this->returnValue(2));
        $this->registryMock->expects($this->any())
            ->method('registry')
            ->with('category')
            ->will($this->returnValue($categoryMock));

        $this->catalogEventHelperMock->expects($this->any())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $this->authorizationMock->expects($this->any())
            ->method('isAllowed')
            ->with('Magento_CatalogEvent::events')
            ->will($this->returnValue(true));

        $eventMock = $this->getMock('Magento\CatalogEvent\Model\Event', ['getId'], [], '', false);
        $eventMock->expects($this->any())->method('getId')->will($this->returnValue($eventId));

        $this->block->setData('event', $eventMock);

        $formBlockMock->expects($this->atLeastOnce())->method('addAdditionalButton')->with($expectedButton);

        $this->block->addButtons();

    }

    public function testAddButtonsDataProvider()
    {
        return [
            [1, 'edit_event'],
            [null, 'add_event'],
        ];
    }
}
