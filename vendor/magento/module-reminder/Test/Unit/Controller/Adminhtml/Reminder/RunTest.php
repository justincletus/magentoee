<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reminder\Test\Unit\Controller\Adminhtml\Reminder;

class RunTest extends AbstractReminderControllerTest
{
    public function testExecute()
    {
        $this->initRule();

        $this->rule->expects($this->at(1))->method('sendReminderEmails')->willReturn(true);
        $this->messageManager->expects($this->once())
            ->method('addSuccess')
            ->with(__('You matched the reminder rule.'))
            ->willReturn(true);
        $this->request->expects($this->any())->method('getParam')->with('id', 0)->willReturn(0);
        $this->redirect('adminhtml/*/edit', ['id' => 0, 'active_tab' => 'matched_customers']);

        $model = new \Magento\Reminder\Controller\Adminhtml\Reminder\Run(
            $this->context,
            $this->coreRegistry,
            $this->ruleFactory,
            $this->conditionFactory,
            $this->dataFilter
        );
        $model->execute();
    }

    public function testExecuteWithException()
    {

        $this->initRuleWithException();
        $this->messageManager->expects($this->once())
            ->method('addError')->with(__('Please correct the reminder rule you requested.'));
        $this->request->expects($this->at(1))->method('getParam')->willReturn(0);
        $this->redirect('adminhtml/*/edit', ['id' => 0, 'active_tab' => 'matched_customers']);

        $model = new \Magento\Reminder\Controller\Adminhtml\Reminder\Run(
            $this->context,
            $this->coreRegistry,
            $this->ruleFactory,
            $this->conditionFactory,
            $this->dataFilter
        );
        $model->execute();
    }

    public function testExecuteWithException2()
    {
        $this->initRuleWithException();
        $this->ruleFactory->expects($this->once())
            ->method('create')->will($this->throwException(new \Exception('Exception massage')));
        $this->messageManager->expects($this->once())
            ->method('addException');
        $this->objectManagerMock->expects($this->once())
            ->method('get')->with('Psr\Log\LoggerInterface')->willReturn($this->logger);
        $this->request->expects($this->at(1))->method('getParam')->willReturn(0);
        $this->redirect('adminhtml/*/edit', ['id' => 0, 'active_tab' => 'matched_customers']);

        $model = new \Magento\Reminder\Controller\Adminhtml\Reminder\Run(
            $this->context,
            $this->coreRegistry,
            $this->ruleFactory,
            $this->conditionFactory,
            $this->dataFilter
        );
        $model->execute();
    }
}
