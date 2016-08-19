<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerCustomAttributes\Test\Unit\Controller\Adminhtml\Customer\Attribute;

use Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Attribute\Validate;
use Magento\Store\Model\WebsiteFactory;

class ValidateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eavConfig;

    /**
     * @var \Magento\Customer\Model\AttributeFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrSetFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /** @var  WebsiteFactory |\PHPUnit_Framework_MockObject_MockObject */
    protected $websiteFactory;

    public function setUp()
    {
        $this->coreRegistry = $this->getMock('Magento\Framework\Registry', [], [], '', false);
        $this->eavConfig = $this->getMock('Magento\Eav\Model\Config', [], [], '', false);
        $this->attrFactory = $this->getMock('Magento\Customer\Model\AttributeFactory', ['create'], [], '', false);
        $this->attrSetFactory = $this->getMock(
            'Magento\Eav\Model\Entity\Attribute\SetFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->request = $this->getMockForAbstractClass(
            'Magento\Framework\App\RequestInterface',
            [],
            '',
            false
        );
        $this->response = $this->getMockForAbstractClass(
            'Magento\Framework\App\ResponseInterface',
            [],
            '',
            false,
            true,
            true,
            ['representJson', 'sendResponse']
        );

        $this->websiteFactory = $this->getMockBuilder('Magento\Store\Model\WebsiteFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->initContext();
    }

    protected function initContext()
    {
        $objectHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->context = $objectHelper->getObject(
            'Magento\Backend\App\Action\Context',
            [
                'request' => $this->request,
                'response' => $this->response
            ]
        );
    }

    public function testExecute()
    {
        $this->request->expects($this->at(0))
            ->method('getParam')
            ->with('attribute_id')
            ->willReturn(false);
        $this->request->expects($this->at(1))
            ->method('getParam')
            ->with('attribute_code')
            ->willReturn('firstname');
        $this->request->expects($this->at(2))
            ->method('getParam')
            ->with('website')
            ->willReturn(1);
        $attribute = $this->getMock('Magento\Customer\Model\Attribute', [], [], '', false);
        $attribute->expects($this->once())
            ->method('loadByCode')
            ->willReturnSelf();
        $attribute->expects($this->once())
            ->method('getId')
            ->willReturn(47);
        $this->attrFactory->expects($this->once())
            ->method('create')
            ->willReturn($attribute);

        $entityType = $this->getMock('Magento\Eav\Model\Entity\Type', [], [], '', false);
        $entityType->expects($this->once())
            ->method('getId')
            ->willReturn(23);
        $this->eavConfig->expects($this->once())
            ->method('getEntityType')
            ->willReturn($entityType);

        $this->response->expects($this->once())
            ->method('representJson')
            ->willReturnSelf();

        $controller = new Validate(
            $this->context,
            $this->coreRegistry,
            $this->eavConfig,
            $this->attrFactory,
            $this->attrSetFactory,
            $this->websiteFactory
        );

        $controller->execute();
    }
}
