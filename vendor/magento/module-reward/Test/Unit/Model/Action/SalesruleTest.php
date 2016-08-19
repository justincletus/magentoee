<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Model\Action;

class SalesruleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardFactoryMock;

    /**
     * @var \Magento\Reward\Model\Action\Salesrule
     */
    protected $model;

    protected function setUp()
    {
        $this->rewardFactoryMock =
            $this->getMock('Magento\Reward\Model\ResourceModel\RewardFactory', [], [], '', false);
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject(
            'Magento\Reward\Model\Action\Salesrule',
            ['rewardData' => $this->rewardFactoryMock]
        );
    }

    public function testCanAddRewardPoints()
    {
        $this->assertTrue($this->model->canAddRewardPoints());
    }

    /**
     * @param array $args
     * @param string $expectedResult
     *
     * @dataProvider getHistoryMessageDataProvider
     */
    public function testGetHistoryMessage(array $args, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->model->getHistoryMessage($args));
    }

    /**
     * @return array
     */
    public function getHistoryMessageDataProvider()
    {
        return [
            [
                'args' => [],
                'expectedResult' => 'Earned promotion extra points from order #',
            ],
            [
                'args' => ['increment_id' => 1],
                'expectedResult' => 'Earned promotion extra points from order #1'
            ]
        ];
    }
}
