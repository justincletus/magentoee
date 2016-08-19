<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reminder\Test\Unit\Controller\Adminhtml\Reminder;

class SaveTest extends AbstractReminderControllerTest
{
    /**
     * @var \Magento\Reminder\Controller\Adminhtml\Reminder\Save|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    public function setUp()
    {
        parent::setUp();

        $this->model = new \Magento\Reminder\Controller\Adminhtml\Reminder\Save(
            $this->context,
            $this->coreRegistry,
            $this->ruleFactory,
            $this->conditionFactory,
            $this->dataFilter
        );

    }

    /**
     * Test with empty data variable
     *
     * @return void
     */
    public function testExecuteWithoutData()
    {
        $this->request->expects($this->once())
            ->method('getPostValue')
            ->willReturn([]);

        $this->redirect('adminhtml/*/');

        $this->model->execute();
    }

    /**
     * Test with set data variable
     *
     * @param array $params
     * @dataProvider executeDataProvider()
     * @return void
     */
    public function testExecuteWithData($params)
    {
        $data = ['param1' => 1, 'param2' => 2, 'rule' => ['conditions' => 'yes', 'actions' => 'action']];

        $this->request->expects($this->once())
            ->method('getPostValue')
            ->willReturn($data);

        $this->request->expects($this->any())->method('getParam')->will(
            $this->returnValueMap(
                [
                    ['back', false, $params['redirectBack']],
                    ['rule_id', null, $params['ruleId']]
                ]
            )
        );
        $this->initRule();
        $model = $this->rule;

        $model->expects($this->exactly($params['validateData']))
            ->method('validateData')->willReturn($params['validateResult']);

        $this->session->expects($this->exactly($params['setPageData']))
            ->method('setPageData')->with(false)->willReturn(1);
        $model->expects($this->exactly(1))->method('save')->willReturn(1);

        $this->session->expects($this->exactly($params['setFormData']))
            ->method('setPageData')->with(false)->willReturn(1);
        if ($params['redirectBack']) {
            $this->redirect('adminhtml/*/edit', ['id' => 1, '_current' => true]);
        }
        $this->messageManager->expects($this->exactly($params['addSuccess']))
            ->method('addSuccess')->with(__('You saved the reminder rule.'))->willReturn(true);
        $this->messageManager->expects($this->exactly($params['addError']))->method('addError')->willReturn(true);

        $this->model->execute();
    }

    public function testExecuteValidationError()
    {
        $data = ['param1' => 1, 'param2' => 2, 'rule' => ['conditions' => 'yes', 'actions' => 'action']];

        $this->request->expects($this->once())
            ->method('getPostValue')
            ->willReturn($data);

        $this->request->expects($this->any())->method('getParam')->will(
            $this->returnValueMap(
                [
                    ['back', false, false],
                    ['rule_id', null, 1]
                ]
            )
        );
        $this->initRule();
        $model = $this->rule;

        $model->expects($this->exactly(1))
            ->method('validateData')->willReturn([__('Validate error 1'), __('Validate error 2')]);
        $model->expects($this->exactly(2))->method('getId')->willReturn(1);
        $this->messageManager->expects($this->exactly(2))->method('addError')->willReturn(true);
        $this->session->expects($this->any())
            ->method('setFormData')->willReturn(1);
        $this->redirect('adminhtml/*/edit', ['id' => $model->getId()]);

        $this->model->execute();
    }

    public function testExecuteWithException()
    {
        $data = ['param1' => 1, 'param2' => 2, 'rule' => ['conditions' => 'yes', 'actions' => 'action']];

        $this->request->expects($this->once())
            ->method('getPostValue')
            ->willReturn($data);

        $this->request->expects($this->any())->method('getParam')->will(
            $this->returnValueMap(
                [
                    ['back', false, false],
                    ['rule_id', null, 1]
                ]
            )
        );
        $this->initRule();
        $model = $this->rule;

        $model->expects($this->exactly(1))
            ->method('validateData')->willReturn(true);

        $model->expects($this->exactly(1))->method('loadPost')->willReturn(1);
        $this->session->expects($this->never())
            ->method('setPageData')->willReturn(1);

        $this->messageManager->expects($this->exactly(1))
            ->method('addError')->with(__('We could not save the reminder rule.'))->willReturn(true);
        $exceptionMock = new \Exception();
        $model->expects($this->exactly(1))
            ->method('save')->willThrowException($exceptionMock);
        $this->objectManagerMock->expects($this->once())
            ->method('get')->with('Psr\Log\LoggerInterface')->willReturn($this->logger);
        $this->logger->expects($this->once())->method('critical')->with($exceptionMock)->willReturn(0);
        $this->redirect('adminhtml/*/');

        $this->model->execute();
    }

    public function testExecuteWithLocalizedException()
    {
        $data = ['param1' => 1, 'param2' => 2, 'rule' => ['conditions' => 'yes', 'actions' => 'action']];

        $this->request->expects($this->once())
            ->method('getPostValue')
            ->willReturn($data);

        $this->request->expects($this->any())->method('getParam')->will(
            $this->returnValueMap(
                [
                    ['back', false, false],
                    ['rule_id', null, 1]
                ]
            )
        );
        $this->initRule();
        $model = $this->rule;

        $model->expects($this->exactly(1))
            ->method('validateData')->willReturn(true);

        $model->expects($this->exactly(1))->method('loadPost')->willReturn(1);
        $this->session->expects($this->any())
            ->method('setPageData')->with($data)->willReturn(1);

        $exceptionMock = new \Magento\Framework\Exception\LocalizedException(__('LocalizedException'));
        $model->expects($this->exactly(1))
            ->method('save')->willThrowException($exceptionMock);

        $this->messageManager->expects($this->exactly(1))
            ->method('addError')->with($exceptionMock->getMessage())->willReturn(true);

        $model->expects($this->exactly(2))->method('getId')->willReturn(1);
        $this->redirect('adminhtml/*/edit', ['id' => $model->getId()]);

        $this->model->execute();
    }

    /**
     * Data provider for test
     * @return array
     */
    public function executeDataProvider()
    {
        return [
            'case1' => [[
                'redirectBack' => false,
                'ruleId' => 1,
                'getId' => [1, 1],
                'modelLoad' => 1,
                'setFormData' => 0,
                'setPageData' => 0,
                'validateData' => 1,
                'validateResult' => true,
                'addSuccess' => 1,
                'addError' => 0,
                'addException' => 0,
                'addException2' => 0
            ]],
            'case2' => [[
                'redirectBack' => true,
                'ruleId' => 1,
                'getId' => [2, 1],
                'modelLoad' => 1,
                'setFormData' => 0,
                'setPageData' => 0,
                'validateData' => 1,
                'validateResult' => true,
                'addSuccess' => 1,
                'addError' => 0,
                'addException' => 0,
                'addException2' => 0
            ]]
        ];
    }
}
