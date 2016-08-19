<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Test\Unit\Controller\Returns;

class AddCommentTest extends \Magento\Rma\Test\Unit\Controller\ReturnsTest
{
    /**
     * @var string
     */
    protected $name = 'AddComment';

    /**
     * @var \Magento\Framework\Controller\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirect;

    /**
     * @var \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactory;

    /**
     * @var \Magento\Rma\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaHelper;

    /**
     * @var \Magento\Rma\Model\Rma|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rma;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $session;

    /**
     * @var \Magento\Rma\Model\Rma\Status\History|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $history;

    protected function initContext()
    {
        $entityId = 7;
        $customerId = 8;
        $comment = 'comment';

        parent::initContext();

        $this->resultRedirect = $this->getMockBuilder('Magento\Framework\Controller\Result\Redirect')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultFactory = $this->getMockBuilder('Magento\Framework\Controller\ResultFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->resultFactory->expects($this->atLeastOnce())
            ->method('create')
            ->with(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)
            ->willReturn($this->resultRedirect);

        $this->context->expects($this->once())
            ->method('getResultFactory')
            ->willReturn($this->resultFactory);

        $this->request->expects($this->any())
            ->method('getParam')
            ->with('entity_id')
            ->willReturn($entityId);
        $this->request->expects($this->any())
            ->method('getPost')
            ->with('comment')
            ->willReturn($comment);

        $this->resultRedirect
            ->expects($this->once())
            ->method('setPath')
            ->with('*/*/view')
            ->willReturn($this->resultRedirect);

        $this->rmaHelper = $this->getMock('Magento\Rma\Helper\Data', [], [], '', false);
        $this->rmaHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->rma = $this->getMock(
            'Magento\Rma\Model\Rma',
            ['__wakeup', 'load', 'getCustomerId', 'getId'],
            [],
            '',
            false
        );
        $this->rma->expects($this->once())
            ->method('load')
            ->with($entityId)
            ->willReturnSelf();
        $this->rma->expects($this->any())
            ->method('getId')
            ->willReturn($entityId);
        $this->rma->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->session = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);
        $this->session->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->history = $this->getMock('Magento\Rma\Model\Rma\Status\History', [], [], '', false);
        $this->history->expects($this->once())
            ->method('sendCustomerCommentEmail');

        $this->history->expects($this->once())
            ->method('saveComment')
            ->with($comment, true, false);
        $this->history->expects($this->once())
            ->method('setRmaEntityId')
            ->with($entityId)
            ->willReturnSelf();
        $this->objectManager->expects($this->at(0))
            ->method('get')
            ->with('Magento\Rma\Helper\Data')
            ->willReturn($this->rmaHelper);
        $this->objectManager->expects($this->at(1))
            ->method('create')
            ->with('Magento\Rma\Model\Rma')
            ->willReturn($this->rma);
        $this->objectManager->expects($this->at(2))
            ->method('get')
            ->with('Magento\Customer\Model\Session')
            ->willReturn($this->session);
        $this->objectManager->expects($this->at(3))
            ->method('create')
            ->with('Magento\Rma\Model\Rma\Status\History')
            ->willReturn($this->history);
    }

    public function testAddCommentAction()
    {
        $this->coreRegistry->expects($this->atLeastOnce())
            ->method('registry')
            ->with('current_rma')
            ->willReturn($this->rma);
        $this->assertSame($this->resultRedirect, $this->controller->execute());
    }
}
