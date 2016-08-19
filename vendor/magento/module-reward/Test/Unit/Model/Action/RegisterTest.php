<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Model\Action;

class RegisterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardDataMock;

    /**
     * @var \Magento\Reward\Model\Action\Register
     */
    protected $model;

    protected function setUp()
    {
        $this->rewardDataMock = $this->getMock('\Magento\Reward\Helper\Data', [], [], '', false);
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject(
            '\Magento\Reward\Model\Action\Register',
            ['rewardData' => $this->rewardDataMock]
        );
    }

    public function testGetPoints()
    {
        $websiteId = 100;
        $this->rewardDataMock->expects($this->once())
            ->method('getPointsConfig')
            ->with('register', $websiteId)
            ->willReturn(500);
        $this->assertEquals(500, $this->model->getPoints($websiteId));
    }

    public function testGetHistoryMessage()
    {
        $this->assertEquals('Registered as customer', $this->model->getHistoryMessage());
    }
}
