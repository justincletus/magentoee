<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\ResourceModel\Report\Environment;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class OsEnvironmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Support\Model\ResourceModel\Report\Environment\OsEnvironment
     */
    protected $osEnvironment;

    /**
     * @var \Magento\Support\Model\ResourceModel\Report\Environment\PhpInfo|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $phpInfoMock;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->phpInfoMock = $this->getMockBuilder('Magento\Support\Model\ResourceModel\Report\Environment\PhpInfo')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param array $phpInfo
     * @param array $expectedResult
     * @return void
     * @dataProvider getOsEnvironmentDataProvider
     */
    public function testGetOsEnvironment($phpInfo, $expectedResult)
    {
        $this->phpInfoMock->expects($this->any())
            ->method('getCollectPhpInfo')
            ->willReturn($phpInfo);

        $this->osEnvironment = $this->objectManagerHelper->getObject(
            'Magento\Support\Model\ResourceModel\Report\Environment\OsEnvironment',
            ['phpInfo' => $this->phpInfoMock]
        );

        $this->assertSame($expectedResult, $this->osEnvironment->getOsInformation());
    }

    /**
     * @return array
     */
    public function getOsEnvironmentDataProvider()
    {
        return [
            [
                'phpInfo' => ['General' => ['System' => 'Test information']],
                'expectedResult' => ['OS Information', 'Test information']
            ],
            [
                'phpInfo' => ['General' => []],
                'expectedResult' => []
            ],
            [
                'phpInfo' => [],
                'expectedResult' => []
            ],
        ];
    }
}
