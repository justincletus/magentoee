<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Test\Unit\Model\Service;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class RmaAttributesManagementTest
 */
class RmaAttributesManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Customer\Model\AttributeMetadataDataProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataDataProviderMock;

    /**
     * @var \Magento\Customer\Model\AttributeMetadataConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataConverterMock;

    /**
     * @var \Magento\Rma\Model\Service\RmaAttributesManagement
     */
    protected $rmaAttributesManagement;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->metadataDataProviderMock = $this->getMock(
            'Magento\Customer\Model\AttributeMetadataDataProvider',
            [],
            [],
            '',
            false
        );
        $this->metadataConverterMock = $this->getMock(
            'Magento\Customer\Model\AttributeMetadataConverter',
            [],
            [],
            '',
            false
        );

        $this->rmaAttributesManagement = $this->objectManager->getObject(
            'Magento\Rma\Model\Service\RmaAttributesManagement',
            [
                'metadataDataProvider' => $this->metadataDataProviderMock,
                'metadataConverter' => $this->metadataConverterMock,
            ]
        );
    }

    /**
     * Run test getAttributes method
     *
     * @return void
     */
    public function testGetAttributes()
    {
        $expectedAttributes = ['attribute-code' => 'metadata'];
        $attributeMock = $this->getMock(
            'Magento\Customer\Model\Attribute',
            [],
            [],
            '',
            false
        );

        $this->metadataDataProviderMock->expects($this->once())
            ->method('loadAttributesCollection')
            ->willReturn([$attributeMock]);
        $attributeMock->expects($this->once())
            ->method('getAttributeCode')
            ->willReturn('attribute-code');
        $this->metadataConverterMock->expects($this->once())
            ->method('createMetadataAttribute')
            ->with($attributeMock)
            ->willReturn('metadata');

        $this->assertEquals($expectedAttributes, $this->rmaAttributesManagement->getAttributes('form-code'));
    }

    /**
     * Run test getAttributeMetadata method
     *
     * @return void
     */
    public function testGetAttributeMetadata()
    {
        $expectedAttributeMetadata = 'result-metadata';
        $attributeMock = $this->getMockForAbstractClass(
            'Magento\Eav\Model\Entity\Attribute\AbstractAttribute',
            [],
            '',
            false,
            true,
            true,
            ['getIsVisible']
        );
        $this->metadataDataProviderMock->expects($this->once())
            ->method('getAttribute')
            ->willReturn($attributeMock);
        $attributeMock->expects($this->atLeastOnce())
            ->method('getIsVisible')
            ->willReturn(1);
        $this->metadataConverterMock->expects($this->once())
            ->method('createMetadataAttribute')
            ->with($attributeMock)
            ->willReturn($expectedAttributeMetadata);

        $this->assertEquals($expectedAttributeMetadata, $this->rmaAttributesManagement->getAttributeMetadata('code'));
    }

    /**
     * Run test getAttributeMetadata method [Exception]
     *
     * @return void
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetAttributeMetadataException()
    {
        $this->metadataDataProviderMock->expects($this->once())
            ->method('getAttribute')
            ->willReturn(null);

        $this->rmaAttributesManagement->getAttributeMetadata('code');
    }

    /**
     * Run test getAllAttributesMetadata method
     *
     * @return void
     */
    public function testGetAllAttributesMetadata()
    {
        $attributeCodes = ['test-code'];
        $attributeMock = $this->getMockForAbstractClass(
            'Magento\Eav\Model\Entity\Attribute\AbstractAttribute',
            [],
            '',
            false,
            true,
            true,
            ['getIsVisible']
        );

        $this->metadataDataProviderMock->expects($this->once())
            ->method('getAllAttributeCodes')
            ->willReturn($attributeCodes);
        $this->metadataDataProviderMock->expects($this->once())
            ->method('getAttribute')
            ->willReturn($attributeMock);
        $attributeMock->expects($this->atLeastOnce())
            ->method('getIsVisible')
            ->willReturn(1);
        $this->metadataConverterMock->expects($this->once())
            ->method('createMetadataAttribute')
            ->with($attributeMock)
            ->willReturn('test-code');

        $this->assertEquals($attributeCodes, $this->rmaAttributesManagement->getAllAttributesMetadata());
    }

    /**
     * Run test getCustomAttributesMetadata method
     *
     * @return void
     */
    public function testGetCustomAttributesMetadata()
    {
        $attributeMetadataMock = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\AttributeMetadataInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $attributeMock = $this->getMockForAbstractClass(
            'Magento\Eav\Model\Entity\Attribute\AbstractAttribute',
            [],
            '',
            false,
            true,
            true,
            ['getIsVisible']
        );

        $attributeCodes = [$attributeMetadataMock];
        $this->metadataDataProviderMock->expects($this->once())
            ->method('getAllAttributeCodes')
            ->willReturn($attributeCodes);
        $this->metadataDataProviderMock->expects($this->once())
            ->method('getAttribute')
            ->willReturn($attributeMock);
        $attributeMock->expects($this->atLeastOnce())
            ->method('getIsVisible')
            ->willReturn(1);
        $this->metadataConverterMock->expects($this->once())
            ->method('createMetadataAttribute')
            ->with($attributeMock)
            ->willReturn($attributeMetadataMock);
        $attributeMetadataMock->expects($this->once())
            ->method('getAttributeCode')
            ->willReturn('get_custom_attributes');

        $this->assertEquals(
            [
                $attributeMetadataMock,
            ],
            $this->rmaAttributesManagement->getCustomAttributesMetadata()
        );
    }
}
