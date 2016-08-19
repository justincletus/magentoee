<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Observer\Backend;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class RemoveVersionCallbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\VersionsCms\Observer\Backend\RemoveVersionCallback
     */
    protected $unit;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->unit = $this->objectManagerHelper->getObject(
            'Magento\VersionsCms\Observer\Backend\RemoveVersionCallback'
        );
    }

    /**
     * @return void
     */
    public function testRemoveVersionCallbackWithLocalizedException()
    {
        $row = 'some row`s data';
        $e = new \Magento\Framework\Exception\LocalizedException(__('Some error'));

        /** @var \Magento\VersionsCms\Model\Page\Version|MockObject $versionMock */
        $versionMock = $this->getMockBuilder('Magento\VersionsCms\Model\Page\Version')
            ->disableOriginalConstructor()
            ->setMethods(['setData', 'delete', 'setAccessLevel', 'save'])
            ->getMock();
        $versionMock->expects($this->once())
            ->method('setData')
            ->with($row);
        $versionMock->expects($this->once())
            ->method('delete')
            ->willThrowException($e);
        $versionMock->expects($this->once())
            ->method('setAccessLevel')
            ->with(\Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PROTECTED);
        $versionMock->expects($this->once())
            ->method('save');

        $args = ['version' => $versionMock, 'row' => $row];
        $this->unit->execute($args);
    }

    /**
     * @return void
     */
    public function testRemoveVersionCallback()
    {
        $row = 'some row`s data';

        /** @var \Magento\VersionsCms\Model\Page\Version|MockObject $versionMock */
        $versionMock = $this->getMockBuilder('Magento\VersionsCms\Model\Page\Version')
            ->disableOriginalConstructor()
            ->setMethods(['setData', 'delete', 'setAccessLevel', 'save'])
            ->getMock();
        $versionMock->expects($this->once())
            ->method('setData')
            ->with($row);
        $versionMock->expects($this->once())
            ->method('delete');
        $versionMock->expects($this->never())
            ->method('setAccessLevel');
        $versionMock->expects($this->never())
            ->method('save');

        $args = ['version' => $versionMock, 'row' => $row];
        $this->unit->execute($args);
    }
}
