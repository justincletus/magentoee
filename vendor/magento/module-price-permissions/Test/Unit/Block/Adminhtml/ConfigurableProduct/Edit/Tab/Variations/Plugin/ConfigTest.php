<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PricePermissions\Test\Unit\Block\Adminhtml\ConfigurableProduct\Edit\Tab\Variations\Plugin;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PricePermissions\Block\Adminhtml\ConfigurableProduct\Product\Edit\Tab\Variations\Plugin\Config
     */
    protected $config;

    /**
     * @var \Magento\Backend\Model\Auth\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $authSession;

    /**
     * @var \Magento\User\Model\User|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $user;

    /**
     * @var \Magento\PricePermissions\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pricePermData;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->authSession = $this->getMock(
            'Magento\Backend\Model\Auth\Session',
            ['isLoggedIn', 'getUser'],
            [],
            '',
            false
        );
        $this->pricePermData = $this->getMock(
            'Magento\PricePermissions\Helper\Data',
            [],
            [],
            '',
            false
        );

        $this->user = $this->getMock('Magento\User\Model\User', [], [], '', false);

        $this->config = $this->objectManager->getObject(
            'Magento\PricePermissions\Block\Adminhtml\ConfigurableProduct\Product\Edit\Tab\Variations\Plugin\Config',
            [
                'authSession' => $this->authSession,
                'pricePermData' => $this->pricePermData,
            ]
        );
    }

    public function testBeforeToHtmlWithPermissions()
    {
        $subject = $this->getMock(
            'Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Variations\Config',
            [],
            [],
            '',
            false
        );
        $this->user->expects($this->any())->method('getRole')->will($this->returnValue('admin'));
        $this->authSession->expects($this->any())->method('isLoggedIn')->willReturn(true);
        $this->authSession->expects($this->any())->method('getUser')->willReturn($this->user);
        $this->pricePermData->expects($this->once())->method('getCanAdminReadProductPrice')->willReturn(true);
        $this->pricePermData->expects($this->once())->method('getCanAdminEditProductPrice')->willReturn(true);
        $subject->expects($this->never())->method('setCanEditPrice');
        $subject->expects($this->never())->method('setCanReadPrice');

        $this->config->beforeToHtml($subject);
    }

    public function testBeforeToHtmlWithoutPermissions()
    {
        $subject = $this->getMock(
            'Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Variations\Config',
            ['setCanEditPrice', 'setCanReadPrice'],
            [],
            '',
            false
        );
        $this->user->expects($this->any())->method('getRole')->will($this->returnValue('admin'));
        $this->authSession->expects($this->any())->method('isLoggedIn')->willReturn(true);
        $this->authSession->expects($this->any())->method('getUser')->willReturn($this->user);
        $this->pricePermData->expects($this->once())->method('getCanAdminReadProductPrice')->willReturn(false);
        $this->pricePermData->expects($this->once())->method('getCanAdminEditProductPrice')->willReturn(false);
        $subject->expects($this->once())->method('setCanEditPrice')->with(false);
        $subject->expects($this->once())->method('setCanReadPrice')->with(false);

        $this->config->beforeToHtml($subject);
    }
}
