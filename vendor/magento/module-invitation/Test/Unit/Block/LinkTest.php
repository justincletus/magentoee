<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Invitation\Test\Unit\Block;

class LinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $_objectManagerHelper;

    protected function setUp()
    {
        $this->_objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
    }

    public function testGetHref()
    {
        $url = 'http://test.exmaple.com/test';

        $invitationHelper = $this->getMockBuilder(
            'Magento\Invitation\Helper\Data'
        )->disableOriginalConstructor()->getMock();

        $invitationHelper->expects(
            $this->once()
        )->method(
            'getCustomerInvitationFormUrl'
        )->will(
            $this->returnValue($url)
        );

        $block = $this->_objectManagerHelper->getObject(
            'Magento\Invitation\Block\Link',
            ['invitationHelper' => $invitationHelper]
        );
        $this->assertEquals($url, $block->getHref());
    }

    /**
     * @return array
     */
    public static function dataForToHtmlTest()
    {
        return [[true, false], [false, true], [false, false]];
    }

    /**
     * @dataProvider dataForToHtmlTest
     * @param bool $isLoggedIn
     * @param bool $isEnabledOnFront
     */
    public function testToHtml($isLoggedIn, $isEnabledOnFront)
    {
        /** @var \Magento\Customer\Model\Session $customerSession |PHPUnit_Framework_MockObject_MockObject */
        $customerSession = $this->getMockBuilder(
            'Magento\Customer\Model\Session'
        )->disableOriginalConstructor()->getMock();

        /** @var \Magento\Invitation\Model\Config $invitationConfig |PHPUnit_Framework_MockObject_MockObject */
        $invitationConfig = $this->getMockBuilder(
            'Magento\Invitation\Model\Config'
        )->disableOriginalConstructor()->getMock();

        /** @var \Magento\Invitation\Block\Link $block */
        $block = $this->_objectManagerHelper->getObject(
            'Magento\Invitation\Block\Link',
            ['customerSession' => $customerSession, 'invitationConfiguration' => $invitationConfig]
        );

        $customerSession->expects($this->any())->method('isLoggedIn')->will($this->returnValue($isLoggedIn));

        $invitationConfig->expects(
            $this->any()
        )->method(
            'isEnabledOnFront'
        )->will(
            $this->returnValue($isEnabledOnFront)
        );

        $this->assertEquals('', $block->toHtml());
    }
}
