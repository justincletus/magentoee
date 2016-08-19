<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Test\Unit\Controller\Adminhtml\Rma;

class GetShippingItemsGridTest extends \Magento\Rma\Test\Unit\Controller\Adminhtml\RmaTest
{
    protected $name = 'GetShippingItemsGrid';

    public function testAction()
    {
        $response = 'testResponse';

        $block = $this->getMock(
            'Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping\Grid',
            [],
            [],
            '',
            false
        );
        $block->expects($this->once())
            ->method('toHtml')
            ->willReturn($response);

        $layoutMock = $this->getMock(
            'Magento\Framework\View\Layout',
            ['renderLayout', 'getBlock'],
            [],
            '',
            false
        );
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('magento_rma_getshippingitemsgrid')
            ->will($this->returnValue($block));

        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));

        $this->responseMock->expects($this->once())
            ->method('setBody')
            ->with($response);

        $this->action->execute();
    }
}
