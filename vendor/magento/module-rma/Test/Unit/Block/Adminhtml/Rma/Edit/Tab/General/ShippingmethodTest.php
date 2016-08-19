<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Test\Unit\Block\Adminhtml\Rma\Edit\Tab\General;

/**
 * Test class for Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shippingmethod
 */
class ShippingmethodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shippingmethod
     */
    protected $shippingmethod;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaShippingFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->registryMock = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->rmaShippingFactoryMock = $this->getMockBuilder('Magento\Rma\Model\ShippingFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->shippingmethod = $objectManager->getObject(
            'Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shippingmethod',
            [
                'shippingFactory' => $this->rmaShippingFactoryMock,
                'registry' => $this->registryMock
            ]
        );
    }

    /**
     * @param array $packages
     * @dataProvider packageProvider
     */
    public function testGetPackages($packages)
    {
        $rmaShippingMock = $this->getMockBuilder('Magento\Rma\Model\Shipping')
            ->disableOriginalConstructor()
            ->setMethods(['getPackages', 'getShippingLabelByRma', '__wakeup'])
            ->getMock();
        $this->rmaShippingFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($rmaShippingMock));

        $rmaMock = $this->getMockBuilder('Magento\Rma\Model\Item')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $rmaShippingMock->expects($this->once())
            ->method('getShippingLabelByRma')
            ->with($rmaMock)
            ->will($this->returnSelf());
        $rmaShippingMock->expects($this->once())
            ->method('getPackages')
            ->will($this->returnValue(serialize($packages)));

        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('current_rma')
            ->will($this->returnValue($rmaMock));

        $this->assertEquals($packages, $this->shippingmethod->getPackages());
    }

    public function packageProvider()
    {
        return [
            [[]],
            [['test']],
            [['package' => ['test']]]
        ];
    }
}
