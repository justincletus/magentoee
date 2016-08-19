<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GoogleTagManager\Test\Unit\Block\Plugin\Checkout\Cart;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class SidebarTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GoogleTagManager\Block\Plugin\Checkout\Cart\Sidebar */
    protected $sidebar;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\GoogleTagManager\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $helper;

    protected function setUp()
    {
        $this->helper = $this->getMock('Magento\GoogleTagManager\Helper\Data', [], [], '', false);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->sidebar = $this->objectManagerHelper->getObject(
            'Magento\GoogleTagManager\Block\Plugin\Checkout\Cart\Sidebar',
            [
                'helper' => $this->helper
            ]
        );
    }

    /**
     * @param bool $available
     * @param string $expected
     *
     * @dataProvider afterToHtmlDataProvider
     */
    public function testAfterToHtml($available, $expected)
    {
        $block = $this->getMock('Magento\GoogleTagManager\Block\ListJson', [], [], '', false);
        $block->expects($this->any())->method('toHtml')->willReturn('<script>gtm_code</script>');

        $layout = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);
        $layout->expects($this->any())->method('getBlock')->with('update_cart_analytics')->willReturn($block);

        $sidebar = $this->getMock('Magento\Checkout\Block\Cart\Sidebar', [], [], '', false);
        $sidebar->expects($this->any())->method('getLayout')->willReturn($layout);
        $this->helper->expects($this->atLeastOnce())->method('isTagManagerAvailable')->willReturn($available);

        $result = $this->sidebar->afterToHtml($sidebar, '<div>Sidebar</div>');
        $this->assertEquals($expected, $result);
    }

    public function afterToHtmlDataProvider()
    {
        return [
            [true, '<div>Sidebar</div><script>gtm_code</script>'],
            [false, '<div>Sidebar</div>'],
        ];
    }
}
