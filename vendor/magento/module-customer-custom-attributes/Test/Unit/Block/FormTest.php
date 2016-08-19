<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Test\Unit\Block;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\AddressMetadataInterface;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\Element\Template\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    protected function setUp()
    {
        $this->request = $this->getMockBuilder('Magento\Framework\App\RequestInterface')
            ->getMockForAbstractClass();

        $this->context = $this->getMockBuilder('Magento\Framework\View\Element\Template\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->context->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->request);
    }

    public function testGetRenderer()
    {
        $objectHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $layout = $this->getMock('\Magento\Framework\View\Layout', ['getBlock'], [], '', false);
        $template = $this->getMock(
            '\Magento\Framework\View\Element\Template',
            ['getChildBlock'],
            [],
            '',
            false
        );
        $layout->expects(
            $this->once()
        )->method(
            'getBlock'
        )->with(
            'customer_form_template'
        )->will(
            $this->returnValue($template)
        );
        $renderer = $this->getMock('\Magento\Framework\View\Element\Template', [], [], '', false);
        $template->expects($this->once())->method('getChildBlock')->with('text')->will($this->returnValue($renderer));

        $block = $objectHelper->getObject('Magento\CustomerCustomAttributes\Block\Form');
        $block->setLayout($layout);

        $this->assertEquals($renderer, $block->getRenderer('text'));
    }

    /**
     * @dataProvider getEntityDataProvider
     * @param int $entityId
     * @param string $entityTypeCode
     * @param int $flagRequest
     * @param int $flagLoad
     */
    public function testGetEntity($entityId, $entityTypeCode, $flagRequest, $flagLoad)
    {
        $objectHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $customerSessionMock = $this->getMock(
            'Magento\Customer\Model\Session',
            ['getCustomerId'],
            [],
            '',
            false
        );
        $customerSessionMock->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue($entityId));

        $this->request->expects($this->exactly($flagRequest))
            ->method('getParam')
            ->with('id', null)
            ->will($this->returnValue($entityId));

        $entityTypeMock = $this->getMockBuilder('Magento\Eav\Model\Entity\Type')
            ->disableOriginalConstructor()
            ->getMock();
        $entityTypeMock->expects($this->once())
            ->method('getEntityTypeCode')
            ->willReturn($entityTypeCode);

        $entityMock = $this->getMock('stdClass', ['load', 'getEntityType', 'getEntityTypeCode']);
        $entityMock->expects($this->exactly($flagLoad))
            ->method('load')
            ->with($entityId);
        $entityMock->expects($this->once())
            ->method('getEntityType')
            ->willReturn($entityTypeMock);

        $modelFactoryMock = $this->getMock('Magento\Framework\Data\Collection\ModelFactory', ['create'], [], '', false);
        $modelFactoryMock->expects($this->once())
            ->method('create')
            ->with('stdClass')
            ->will($this->returnValue($entityMock));

        /** @var \Magento\CustomerCustomAttributes\Block\Form $block */
        $block = $objectHelper->getObject(
            'Magento\CustomerCustomAttributes\Block\Form',
            [
                'context' => $this->context,
                'modelFactory' => $modelFactoryMock,
                'customerSession' => $customerSessionMock,
            ]
        );
        $block->setEntityModelClass('stdClass');
        $block->getEntity();
    }

    /**
     * @return array
     */
    public function getEntityDataProvider()
    {
        return [
            [1, CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, 0, 1],
            [1, AddressMetadataInterface::ENTITY_TYPE_ADDRESS, 1, 1],
            [1, null, 0, 0],
        ];
    }
}
