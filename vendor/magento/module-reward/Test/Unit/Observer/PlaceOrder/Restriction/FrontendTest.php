<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Test\Unit\Observer\PlaceOrder\Restriction;

class FrontendTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Observer\PlaceOrder\Restriction\Frontend
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = $this->getMock('Magento\Reward\Helper\Data', [], [], '', false);
        $this->_model = new \Magento\Reward\Observer\PlaceOrder\Restriction\Frontend($this->_helper);
    }

    public function testIsAllowed()
    {
        $this->_helper->expects($this->once())->method('isEnabledOnFront');
        $this->_model->isAllowed();
    }
}
