<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdminGws\Test\Unit\Observer;

class AddDataAfterRoleLoadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdminGws\Observer\AddDataAfterRoleLoad
     */
    protected $_addDataAfterRoleLoadObserver;

    /**
     * @var \Magento\AdminGws\Observer\RefreshRolePermissions|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_rolePermissionAssigner;

    /**
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp()
    {
        $this->_rolePermissionAssigner = $this->getMockBuilder(
            'Magento\AdminGws\Observer\RolePermissionAssigner'
        )->setMethods(
            []
        )->disableOriginalConstructor()->getMock();

        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->_addDataAfterRoleLoadObserver = $objectManagerHelper->getObject(
            'Magento\AdminGws\Observer\AddDataAfterRoleLoad',
            [
                $this->_rolePermissionAssigner
            ]
        );
    }

    public function testAddDataAfterRoleLoad()
    {
        /** @var \Magento\Authorization\Model\Role|\PHPUnit_Framework_MockObject_MockObject $role */
        $role = $this->getMock('Magento\Authorization\Model\Role', [], [], '', false);

        $event = $this->getMock('Magento\Framework\Event', ['getObject'], [], '', false);
        $event->expects($this->once())->method('getObject')->will($this->returnValue($role));
        $observer = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $this->_addDataAfterRoleLoadObserver->execute($observer);
    }
}
