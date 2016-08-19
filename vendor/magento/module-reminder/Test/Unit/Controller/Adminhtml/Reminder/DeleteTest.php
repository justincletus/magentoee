<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reminder\Test\Unit\Controller\Adminhtml\Reminder;

class DeleteTest extends AbstractReminderControllerTest
{
    public function testExecute()
    {
        $this->initRule();

        $this->rule->expects($this->at(1))->method('delete')->willReturn(true);
        $this->messageManager->expects($this->once())
            ->method('addSuccess')
            ->with(__('You deleted the reminder rule.'))
            ->willReturn(true);

        $this->redirect('adminhtml/*/', []);

        $model = new \Magento\Reminder\Controller\Adminhtml\Reminder\Delete(
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
        $this->initRule();
        $exceptionMock = new \Magento\Framework\Exception\LocalizedException(
            __('Please correct the reminder rule you requested.')
        );
        $this->rule->expects($this->once())->method('delete')->will($this->throwException($exceptionMock));
        $this->messageManager->expects($this->once())
            ->method('addError')->with(__('Please correct the reminder rule you requested.'));
        $this->redirect('adminhtml/*/edit', ['id' => 1]);

        $model = new \Magento\Reminder\Controller\Adminhtml\Reminder\Delete(
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
        $exceptionMock = new \Exception('Exception message');
        $this->ruleFactory->expects($this->once())
            ->method('create')->will($this->throwException($exceptionMock));
        $this->messageManager->expects($this->once())
            ->method('addError')->with(__('We can\'t delete the reminder rule right now.'));
        $this->objectManagerMock->expects($this->once())
            ->method('get')->with('Psr\Log\LoggerInterface')->willReturn($this->logger);
        $this->logger->expects($this->once())->method('critical')->with($exceptionMock)->willReturn(0);
        $this->redirect('adminhtml/*/', []);

        $model = new \Magento\Reminder\Controller\Adminhtml\Reminder\Delete(
            $this->context,
            $this->coreRegistry,
            $this->ruleFactory,
            $this->conditionFactory,
            $this->dataFilter
        );
        $model->execute();
    }
}
