<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Model\Action;

class AdminTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Action\Admin
     */
    protected $model;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject('Magento\Reward\Model\Action\Admin');
    }

    public function testCanAddRewardPoints()
    {
        $this->assertTrue($this->model->canAddRewardPoints());
    }

    public function testGetHistoryMessage()
    {
        $this->assertEquals('Updated by moderator', $this->model->getHistoryMessage());
    }
}
