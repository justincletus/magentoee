<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogEvent\Test\Unit\Controller\Adminhtml\Catalog\Event;

use Magento\CatalogEvent\Controller\Adminhtml\Catalog\Event\Edit;
use Magento\Framework\DataObject;
use Magento\Framework\Phrase;

class EditTest extends \Magento\CatalogEvent\Test\Unit\Controller\Adminhtml\Catalog\AbstractEventTest
{
    /**
     * @var \Magento\CatalogEvent\Controller\Adminhtml\Catalog\Event\Edit
     */
    protected $edit;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->edit = new Edit(
            $this->contextMock,
            $this->registryMock,
            $this->eventFactoryMock,
            $this->dateTimeMock,
            $this->storeManagerMock
        );
    }

    /**
     * @param int $eventId
     * @param mixed $prepend
     * @return void
     * @dataProvider executeDataProvider
     */
    public function testExecute($eventId, $prepend)
    {
        $eventMock = $this->getMockBuilder('Magento\CatalogEvent\Model\Event')
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock
            ->expects($this->any())
            ->method('setStoreId')
            ->willReturnSelf();
        $eventMock
            ->expects($this->any())
            ->method('getId')
            ->willReturn($eventId);

        $this->eventFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($eventMock);

        $this->requestMock
            ->expects($this->any())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['id', false, $eventId],
                    ['category_id', null, 999]
                ]
            );

        $this->sessionMock
            ->expects($this->any())
            ->method('getEventData')
            ->willReturn(['some data']);

        $titleMock = $this->getMockBuilder('Magento\Framework\View\Page\Title')
            ->disableOriginalConstructor()
            ->getMock();
        $titleMock
            ->expects($this->exactly(2))
            ->method('prepend')
            ->withConsecutive(
                [new Phrase('Events')],
                [$prepend]
            );

        $this->viewMock
            ->expects($this->any())
            ->method('getPage')
            ->willReturn(new DataObject(['config' => new DataObject(['title' => $titleMock])]));

        $this->switcherBlockMock
            ->expects($this->any())
            ->method('setDefaultStoreName')
            ->willReturnSelf();

        $this->edit->execute();
    }

    /**
     * @return array
     */
    public function executeDataProvider()
    {
        return [
            [123, '#123'],
            [123, '#123'],
            [null, new Phrase('New Event')]
        ];
    }
}
