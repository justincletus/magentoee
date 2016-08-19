<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Model\Action;

class NewsletterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collFactoryMock;

    /**
     * @var \Magento\Reward\Model\Action\Newsletter
     */
    protected $model;

    protected function setUp()
    {
        $this->rewardDataMock = $this->getMock('Magento\Reward\Helper\Data', [], [], '', false);
        $this->collFactoryMock = $this->getMock(
            'Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject(
            'Magento\Reward\Model\Action\Newsletter',
            ['rewardData' => $this->rewardDataMock, 'subscribersFactory' => $this->collFactoryMock]
        );
    }

    public function testGetPoints()
    {
        $websiteId = 100;
        $this->rewardDataMock->expects($this->once())
            ->method('getPointsConfig')
            ->with('newsletter', $websiteId)
            ->willReturn(500);
        $this->assertEquals(500, $this->model->getPoints($websiteId));
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
                'expectedResult' => 'Signed up for newsletter with email ',
            ],
            [
                'args' => ['email' => 'test@mail.com'],
                'expectedResult' => 'Signed up for newsletter with email test@mail.com'
            ]
        ];
    }
}
