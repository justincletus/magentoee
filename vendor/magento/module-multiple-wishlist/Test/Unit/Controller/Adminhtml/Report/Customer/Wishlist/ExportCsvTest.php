<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\MultipleWishlist\Test\Unit\Controller\Adminhtml\Report\Customer\Wishlist;

use Magento\MultipleWishlist\Controller\Adminhtml\Report\Customer\Wishlist\ExportCsv;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportCsvTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\App\Response\Http\FileFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $fileFactory;

    /** @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $context;

    /** @var \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $resultFactory;

    /** @var \Magento\Framework\View\Result\Layout $resultLayout|\PHPUnit_Framework_MockObject_MockObject */
    protected $resultLayout;

    /** @var \Magento\Framework\View\Layout|\PHPUnit_Framework_MockObject_MockObject */
    protected $layout;

    /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $exportGridBlock;

    /** @var ExportCsv */
    protected $controller;

    /** @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $response;

    public function setUp()
    {
        $this->fileFactory = $this->getMock(
            'Magento\Framework\App\Response\Http\FileFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->resultLayout = $this->getMock('Magento\Framework\View\Result\Layout', [], [], '', false);
        $this->layout = $this->getMock('Magento\Framework\View\Layout', [], [], '', false);
        $this->exportGridBlock = $this->getMockForAbstractClass(
            'Magento\Backend\Block\Widget\Grid\ExportInterface',
            [],
            '',
            false
        );
        $this->resultFactory = $this->getMock('Magento\Framework\Controller\ResultFactory', [], [], '', false);
        $this->response = $this->getMockForAbstractClass('Magento\Framework\App\ResponseInterface', [], '', false);

        $objectHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->context = $objectHelper->getObject(
            'Magento\Backend\App\Action\Context',
            [
               'resultFactory' => $this->resultFactory
            ]
        );
        $this->controller = new ExportCsv($this->context, $this->fileFactory);
    }

    public function testExecute()
    {
        $this->resultFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->resultLayout);
        $this->resultLayout->expects($this->once())
            ->method('getLayout')
            ->willReturn($this->layout);
        $this->layout->expects($this->once())
            ->method('getChildBlock')
            ->with('adminhtml.block.report.customer.wishlist.grid', 'grid.export')
            ->willReturn($this->exportGridBlock);
        $this->exportGridBlock->expects($this->once())
            ->method('getCsvFile')
            ->willReturn('csvFile');
        $this->fileFactory->expects($this->once())
            ->method('create')
            ->with('customer_wishlists.csv', 'csvFile', DirectoryList::VAR_DIR)
            ->willReturn($this->response);

        $this->controller->execute();
    }
}
