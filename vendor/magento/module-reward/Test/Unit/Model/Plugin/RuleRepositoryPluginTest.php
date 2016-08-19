<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reward\Test\Unit\Model\Plugin;

class RuleRepositoryPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $extensionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardFactoryMock;

    /**
     * @var \Magento\Reward\Model\Plugin\RuleRepositoryPlugin
     */
    protected $plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleExtensionMock;

    protected function setUp()
    {
        $this->ruleRepositoryMock = $this->getMock('Magento\SalesRule\Model\RuleRepository', [], [], '', false);
        $this->extensionFactoryMock = $this->getMock(
            'Magento\SalesRule\Api\Data\RuleExtensionFactory',
            ['create', '__sleep'],
            [],
            '',
            false
        );
        $this->rewardFactoryMock = $this->getMock(
            'Magento\Reward\Model\ResourceModel\RewardFactory',
            ['create', '__sleep'],
            [],
            '',
            false
        );
        $this->plugin = new \Magento\Reward\Model\Plugin\RuleRepositoryPlugin(
            $this->ruleRepositoryMock,
            $this->extensionFactoryMock,
            $this->rewardFactoryMock
        );

        $this->ruleMock = $this->getMock('Magento\SalesRule\Api\Data\RuleInterface', [], [], '', false);
        $this->rewardMock = $this->getMock('Magento\Reward\Model\ResourceModel\Reward', [], [], '', false);
        $this->ruleExtensionMock = $this->getMock(
            '\Magento\SalesRule\Api\Data\RuleExtensionInterface',
            ['setRewardPointsDelta', 'getRewardPointsDelta', '__sleep'],
            [],
            '',
            false
        );
    }

    public function testAroundGetById()
    {
        $closure = function () {
            return $this->ruleMock;
        };
        $ruleId = 123;
        $pointsDelta = 3000;
        $rewardSalesRule = ['rule_id' => $ruleId, 'points_delta' => $pointsDelta];

        $this->rewardFactoryMock->expects($this->once())->method('create')->willReturn($this->rewardMock);
        $this->rewardMock->expects($this->once())
            ->method('getRewardSalesrule')
            ->with($ruleId)
            ->willReturn($rewardSalesRule);
        $this->ruleMock->expects($this->once())->method('getExtensionAttributes')->willReturn(null);
        $this->extensionFactoryMock->expects($this->once())->method('create')->willReturn($this->ruleExtensionMock);
        $this->ruleExtensionMock->expects($this->once())->method('setRewardPointsDelta')->with($pointsDelta);
        $this->ruleMock->expects($this->once())->method('setExtensionAttributes')->with($this->ruleExtensionMock);

        $result = $this->plugin->aroundGetById($this->ruleRepositoryMock, $closure, $ruleId);
        $this->assertEquals($this->ruleMock, $result);
    }

    public function testSaveWithoutRewardPoints()
    {
        $closure = function () {
            return $this->ruleMock;
        };
        $this->ruleMock->expects($this->exactly(2))
            ->method('getExtensionAttributes')
            ->willReturn($this->ruleExtensionMock);
        $this->ruleExtensionMock->expects($this->once())
            ->method('getRewardPointsDelta')
            ->willReturn(null);
        $this->assertEquals(
            $this->ruleMock,
            $this->plugin->aroundSave(
                $this->ruleRepositoryMock,
                $closure,
                $this->ruleMock
            )
        );
    }

    public function testSave()
    {
        $ruleId = 123;
        $pointsDelta = 3000;
        $closure = function () {
            return $this->ruleMock;
        };
        $this->ruleMock->expects($this->exactly(4))
            ->method('getExtensionAttributes')
            ->willReturn($this->ruleExtensionMock);
        $this->ruleMock->expects($this->once())
            ->method('getRuleId')
            ->willReturn($ruleId);
        $this->ruleExtensionMock->expects($this->exactly(2))
            ->method('getRewardPointsDelta')
            ->willReturn($pointsDelta);
        $this->rewardFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->rewardMock);
        $this->rewardMock->expects($this->once())
            ->method('saveRewardSalesrule')
            ->with($ruleId, $pointsDelta)
            ->willReturn(1);
        $this->ruleExtensionMock->expects($this->once())
            ->method('setRewardPointsDelta')
            ->with($pointsDelta);
        $this->ruleMock->expects($this->once())
            ->method('setExtensionAttributes')
            ->with($this->ruleExtensionMock);

        $this->assertEquals(
            $this->ruleMock,
            $this->plugin->aroundSave(
                $this->ruleRepositoryMock,
                $closure,
                $this->ruleMock
            )
        );
    }
}
