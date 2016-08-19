<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Test\Unit\Model;

class ShippingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Model\Shipping
     */
    protected $model;

    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $carrierFactory;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $orderFactory = $this->getMock('Magento\Sales\Model\OrderFactory', ['create'], [], '', false);
        $regionFactory = $this->getMock('Magento\Directory\Model\RegionFactory', ['create'], [], '', false);
        $this->carrierFactory = $this->getMock('Magento\Shipping\Model\CarrierFactory', [], [], '', false);
        $returnFactory = $this->getMock(
            'Magento\Shipping\Model\Shipment\ReturnShipmentFactory',
            ['create'],
            [],
            '',
            false
        );
        $rmaFactory = $this->getMock('Magento\Rma\Model\RmaFactory', ['create'], [], '', false);
        $filesystem = $this->getMock('Magento\Framework\Filesystem', [], [], '', false, false);

        $this->model = $objectManagerHelper->getObject(
            'Magento\Rma\Model\Shipping',
            [
                'orderFactory' => $orderFactory,
                'regionFactory' => $regionFactory,
                'returnFactory' => $returnFactory,
                'carrierFactory' => $this->carrierFactory,
                'rmaFactory' => $rmaFactory,
                'filesystem' => $filesystem
            ]
        );
    }

    /**
     * @dataProvider isCustomDataProvider
     * @param bool $expectedResult
     * @param string $carrierCodeToSet
     */
    public function testIsCustom($expectedResult, $carrierCodeToSet)
    {
        $this->model->setCarrierCode($carrierCodeToSet);
        $this->assertEquals($expectedResult, $this->model->isCustom());
    }

    /**
     * @return array
     */
    public static function isCustomDataProvider()
    {
        return [
            [true, \Magento\Sales\Model\Order\Shipment\Track::CUSTOM_CARRIER_CODE],
            [false, 'not-custom']
        ];
    }

    public function testGetNumberDetailWithoutCarrierInstance()
    {
        $carrierTitle = 'Carrier Title';
        $trackNumber = 'US1111CA';
        $expected = [
            'title' => $carrierTitle,
            'number' => $trackNumber,
        ];
        $this->model->setCarierTitle($carrierTitle);
        $this->model->setTrackNumber($trackNumber);

        $this->assertEquals($expected, $this->model->getNumberDetail());
    }

    /**
     * @dataProvider getNumberDetailDataProvider
     */
    public function testGetNumberDetail($trackingInfo, $trackNumber, $expected)
    {
        $carrierMock = $this->getMock(
            'Magento\OfflineShipping\Model\Carrier\Flatrate',
            ['getTrackingInfo', 'setStore'],
            [],
            '',
            false
        );
        $this->carrierFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($carrierMock));
        $carrierMock->expects($this->any())
            ->method('getTrackingInfo')
            ->will($this->returnValue($trackingInfo));

        $this->model->setTrackNumber($trackNumber);
        $this->assertEquals($expected, $this->model->getNumberDetail());
    }

    public function getNumberDetailDataProvider()
    {
        $trackNumber = 'US1111CA';
        return [
            'With tracking info' => ['some tracking info', $trackNumber, 'some tracking info'],
            'Without tracking info' => [false, $trackNumber, __('No detail for number "' . $trackNumber . '"')]
        ];
    }
}
