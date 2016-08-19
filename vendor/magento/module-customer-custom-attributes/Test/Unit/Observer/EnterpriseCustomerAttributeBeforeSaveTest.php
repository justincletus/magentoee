<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Test\Unit\Observer;

class EnterpriseCustomerAttributeBeforeSaveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerCustomAttributes\Observer\EnterpriseCustomerAttributeBeforeSave
     */
    protected $observer;

    public function setUp()
    {
        $this->observer = new \Magento\CustomerCustomAttributes\Observer\EnterpriseCustomerAttributeBeforeSave;
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testEnterpriseCustomerAttributeBeforeSaveNegative()
    {
        $attributeData = 'so_long_attribute_code_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Customer\Model\Attribute')
            ->setMethods(['__wakeup', 'isObjectNew', 'getAttributeCode'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(true));

        $dataModel->expects($this->once())
            ->method('getAttributeCode')
            ->will($this->returnValue($attributeData));

        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getAttribute')->will($this->returnValue($dataModel));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->observer->execute($observer);
    }

    public function testEnterpriseCustomerAttributeBeforeSavePositive()
    {
        $attributeData = 'normal_attribute_code';
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Customer\Model\Attribute')
            ->setMethods(['__wakeup', 'isObjectNew', 'getAttributeCode'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(true));

        $dataModel->expects($this->once())
            ->method('getAttributeCode')
            ->will($this->returnValue($attributeData));

        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getAttribute')->will($this->returnValue($dataModel));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Observer\EnterpriseCustomerAttributeBeforeSave',
            $this->observer->execute($observer)
        );
    }
}
