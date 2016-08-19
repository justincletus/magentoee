<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reminder\Test\Unit\Controller\Adminhtml\Reminder;

/**
 * Class AbstractReminderControllerTest
 * @package Magento\Reminder\Test\Unit\Controller\Adminhtml\Reminder
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
abstract class AbstractReminderControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $session;

    /**
     * @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\Message\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManager;

    /**
     * @var \Magento\Reminder\Model\RuleFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleFactory;

    /**
     * @var \Magento\Reminder\Model\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rule;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Backend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendHelper;

    /**
     * @var \Magento\Backend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataFilter;

    /**
     * @var \Magento\Reminder\Model\Rule\ConditionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $conditionFactory;

    /**
     * @var \Magento\Framework\App\ViewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $view;

    /**
     * @var \Magento\Framework\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    /**
     * @var \Magento\Framework\View\Element\BlockInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $block;

    /**
     * @var \Magento\Backend\Model\Menu|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $menuModel;

    /**
     * @var \Magento\Framework\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $page;

    /**
     * @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

    /**
     * @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $titleMock;

    /**
     * @var \Magento\Backend\Model\Menu\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $item;

    /**
     * @var \Magento\Reminder\Model\Rule\ConditionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $condition;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\Framework\App\ActionFlag|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $actionFlag;

    /**
     * @var \Magento\Backend\Model\View\Result\RedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectFactory;

    public function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface', [], [], '', false);
        $this->titleMock =  $this->getMock('Magento\Framework\View\Page\Title', [], [], '', false);
        $this->logger =  $this->getMockForAbstractClass('Psr\Log\LoggerInterface', [], '', false);
        $this->actionFlag = $this->getMock('Magento\Framework\App\ActionFlag', [], [], '', false);

        $this->response = $this->getMock(
            'Magento\Framework\App\ResponseInterface',
            ['setRedirect', 'sendResponse', 'setBody'],
            [],
            '',
            false
        );
        $this->request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->messageManager = $this->getMock('Magento\Framework\Message\Manager', [], [], '', false);

        $this->resultRedirectFactory = $this->getMock(
            'Magento\Backend\Model\View\Result\RedirectFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->session = $this->getMock('Magento\Backend\Model\Session', [], [], '', false);
        $this->dataFilter = $this->getMock('Magento\Framework\Stdlib\DateTime\Filter\Date', [], [], '', false);
        $this->conditionFactory = $this->getMock('Magento\Reminder\Model\Rule\ConditionFactory', [], [], '', false);
        $this->ruleFactory = $this->getMock('Magento\Reminder\Model\RuleFactory', ['create'], [], '', false);

        $this->rule = $this->getMock('Magento\Reminder\Model\Rule', [], [], '', false);
        $this->backendHelper = $this->getMock('Magento\Backend\Helper\Data', [], [], '', false);
        $this->coreRegistry = $this->getMock('Magento\Framework\Registry', [], [], '', false);

        $this->view = $this->getMockForAbstractClass('Magento\Framework\App\ViewInterface', [], '', false);

        $this->layout = $this->getMockForAbstractClass('Magento\Framework\View\LayoutInterface', [], '', false);
        $this->block = $this->getMock(
            'Magento\Framework\View\Element\BlockInterface',
            ['setActive', 'toHtml', 'getMenuModel', 'addLink', 'setData'],
            [],
            '',
            false
        );
        $this->condition = $this->getMock('Magento\Rule\Model\Condition\Combine', [], [], '', false);
        $this->menuModel = $this->getMock('Magento\Backend\Model\Menu', [], [], '', false);
        $this->page = $this->getMock('Magento\Framework\View\Result\Page', [], [], '', false);
        $this->config = $this->getMock('Magento\Framework\View\Page\Config', [], [], '', false);
        $this->item = $this->getMock('\Magento\Backend\Model\Menu\Item', [], [], '', false);

        $this->context = $this->getMock('Magento\Backend\App\Action\Context', [], [], '', false);
        $this->context->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->request);
        $this->context->expects($this->once())
            ->method('getResponse')
            ->willReturn($this->response);
        $this->context->expects($this->once())
            ->method('getObjectManager')
            ->willReturn($this->objectManagerMock);
        $this->context->expects($this->once())
            ->method('getView')
            ->willReturn($this->view);
        $this->context->expects($this->once())
            ->method('getSession')
            ->willReturn($this->session);
        $this->context->expects($this->once())
            ->method('getResponse')
            ->willReturn($this->response);
        $this->context->expects($this->once())
            ->method('getMessageManager')
            ->willReturn($this->messageManager);
        $this->context->expects($this->once())
            ->method('getHelper')
            ->willReturn($this->backendHelper);
        $this->context->expects($this->once())
            ->method('getResultRedirectFactory')
            ->willReturn($this->resultRedirectFactory);
        $this->context->expects($this->once())
            ->method('getActionFlag')->willReturn($this->actionFlag);
    }

    public function initRuleWithException()
    {
        $this->request->expects($this->at(0))->method('getParam')->willReturn(1);
        $this->ruleFactory->expects($this->once())->method('create')->willReturn($this->rule);
        $this->rule->expects($this->any())->method('getId')->willReturn(null);

        $this->coreRegistry->expects($this->never())
            ->method('register')->with('current_reminder_rule', $this->rule)->willReturn(1);
    }

    public function initRule()
    {
        $this->request->expects($this->at(0))->method('getParam')->willReturn(1);
        $this->ruleFactory->expects($this->once())->method('create')->willReturn($this->rule);
        $this->rule->expects($this->any())->method('getId')->willReturn(1);
        $this->coreRegistry->expects($this->any())
                ->method('register')->with('current_reminder_rule', $this->rule);
    }

    public function redirect($path, $args = [])
    {
        $this->actionFlag->expects($this->any())->method('get');
        $this->session->expects($this->any())->method('setIsUrlNotice');
        $this->response->expects($this->once())->method('setRedirect');
        $this->backendHelper->expects($this->once())->method('getUrl')->with($path, $args);
    }
}
