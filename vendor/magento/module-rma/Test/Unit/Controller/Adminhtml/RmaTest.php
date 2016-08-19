<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Rma\Test\Unit\Controller\Adminhtml;

/**
 * Class RmaTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
abstract class RmaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var \Magento\Rma\Controller\Adminhtml\Rma
     */
    protected $action;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\App\Response\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreRegistryMock;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var \Magento\Framework\App\ActionFlag|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $flagActionMock;

    /**
     * @var \Magento\Rma\Model\ResourceModel\Item\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaCollectionMock;

    /**
     * @var \Magento\Rma\Model\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaItemMock;

    /**
     * @var \Magento\Rma\Model\Rma|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaModelMock;

    /**
     * @var \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderMock;

    /**
     * @var \Magento\Rma\Model\Rma\Source\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sourceStatusMock;

    /**
     * @var \Magento\Rma\Model\Rma\Status\History|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusHistoryMock;

    /**
     * @var \Magento\Framework\App\ViewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewMock;

    /**
     * @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $titleMock;

    /**
     * @var \Magento\Framework\Data\Form|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formMock;

    /**
     * @var \Magento\Rma\Model\Rma\RmaDataMapper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaDataMapperMock;

    /**
     * test setUp
     */
    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $contextMock = $this->getMock('Magento\Backend\App\Action\Context', [], [], '', false);
        $backendHelperMock = $this->getMock('Magento\Backend\Helper\Data', [], [], '', false);
        $this->rmaDataMapperMock = $this->getMock('Magento\Rma\Model\Rma\RmaDataMapper', [], [], '', false);
        $this->viewMock = $this->getMock('Magento\Framework\App\ViewInterface', [], [], '', false);
        $this->titleMock = $this->getMock('Magento\Framework\View\Page\Title', [], [], '', false);
        $this->formMock = $this->getMock('Magento\Framework\Data\Form', ['hasNewAttributes', 'toHtml'], [], '', false);
        $this->initMocks();
        $contextMock->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->requestMock));
        $contextMock->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($this->responseMock));
        $contextMock->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManagerMock));
        $contextMock->expects($this->once())
            ->method('getMessageManager')
            ->will($this->returnValue($this->messageManagerMock));
        $contextMock->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($this->sessionMock));
        $contextMock->expects($this->once())
            ->method('getActionFlag')
            ->will($this->returnValue($this->flagActionMock));
        $contextMock->expects($this->once())
            ->method('getHelper')
            ->will($this->returnValue($backendHelperMock));
        $contextMock->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($this->viewMock));

        $arguments = $this->getConstructArguments();
        $arguments['context'] = $contextMock;

        $this->action = $objectManager->getObject(
            'Magento\\Rma\\Controller\\Adminhtml\\Rma\\' . $this->name,
            $arguments
        );
    }

    /**
     * @return array
     */
    protected function getConstructArguments()
    {
        return [
            'coreRegistry' => $this->coreRegistryMock,
            'rmaDataMapper' => $this->rmaDataMapperMock
        ];
    }

    protected function initMocks()
    {
        $this->coreRegistryMock = $this->getMock('Magento\Framework\Registry', [], [], '', false);
        $this->requestMock = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->responseMock = $this->getMock(
            'Magento\Framework\App\Response\Http',
            [
                'setBody',
                'representJson',
                'setRedirect',
                '__wakeup'
            ],
            [],
            '',
            false
        );
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->messageManagerMock = $this->getMock('Magento\Framework\Message\ManagerInterface', [], [], '', false);
        $this->sessionMock = $this->getMock('Magento\Backend\Model\Session', [], [], '', false);
        $this->flagActionMock = $this->getMock('Magento\Framework\App\ActionFlag', [], [], '', false);
        $this->rmaCollectionMock = $this->getMock('Magento\Rma\Model\ResourceModel\Item\Collection', [], [], '', false);
        $this->rmaItemMock = $this->getMock('Magento\Rma\Model\Item', [], [], '', false);
        $this->rmaModelMock = $this->getMock(
            'Magento\Rma\Model\Rma',
            [
                'saveRma',
                'getId',
                'setStatus',
                'load',
                'canClose',
                'close',
                'save',
                '__wakeup'
            ],
            [],
            '',
            false
        );
        $this->orderMock = $this->getMock('Magento\Sales\Model\Order', [], [], '', false);
        $this->sourceStatusMock = $this->getMock('Magento\Rma\Model\Rma\Source\Status', [], [], '', false);
        $this->statusHistoryMock = $this->getMock(
            'Magento\Rma\Model\Rma\Status\History',
            [
                'setRma',
                'setRmaEntityId',
                'sendNewRmaEmail',
                'saveComment',
                'saveSystemComment',
                'setComment',
                'sendAuthorizeEmail',
                'sendCommentEmail',
                '__wakeup'
            ],
            [],
            '',
            false
        );
        $this->objectManagerMock->expects($this->any())
            ->method('create')
            ->will(
                $this->returnValueMap(
                    [
                        ['Magento\Rma\Model\ResourceModel\Item\Collection', [], $this->rmaCollectionMock],
                        ['Magento\Rma\Model\Item', [], $this->rmaItemMock],
                        ['Magento\Rma\Model\Rma', [], $this->rmaModelMock],
                        ['Magento\Sales\Model\Order', [], $this->orderMock],
                        ['Magento\Rma\Model\Rma\Source\Status', [], $this->sourceStatusMock],
                        ['Magento\Rma\Model\Rma\Status\History', [], $this->statusHistoryMock],
                    ]
                )
            );
    }

    protected function initRequestData($commentText = '', $visibleOnFront = true)
    {
        $rmaConfirmation = true;
        $post = [
            'items' => [],
            'rma_confirmation' => $rmaConfirmation,
            'comment' => [
                'comment' => $commentText,
                'is_visible_on_front' => $visibleOnFront,
            ],
        ];
        $this->requestMock->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(true));
        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->will(
                $this->returnValue(
                    [
                        'items' => [],
                        'rma_confirmation' => $rmaConfirmation,
                        'comment' => [
                            'comment' => $commentText,
                            'is_visible_on_front' => $visibleOnFront,
                        ],
                    ]
                )
            );
        return $post;
    }
}
