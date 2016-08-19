<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Model;

class ActionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\ActionFactory
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->objectManagerMock = $this->getMock('\Magento\Framework\ObjectManagerInterface');

        $this->model = $objectManager->getObject(
            'Magento\Reward\Model\ActionFactory',
            ['objectManager' => $this->objectManagerMock]
        );
    }

    public function testCreate()
    {
        $type = 'action_type';
        $params = ['param' => 'value'];
        $actionMock = $this->getMock('\Magento\Reward\Model\Action\AbstractAction');

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with($type, $params)
            ->willReturn($actionMock);

        $this->assertEquals($actionMock, $this->model->create($type, $params));
    }
}
