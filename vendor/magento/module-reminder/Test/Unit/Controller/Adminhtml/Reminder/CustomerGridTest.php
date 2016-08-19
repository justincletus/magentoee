<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reminder\Test\Unit\Controller\Adminhtml\Reminder;

class CustomerGridTest extends AbstractReminderControllerTest
{
    public function testExecute()
    {
        $this->initRule();
        $this->view->expects($this->any())->method('getLayout')->willReturn($this->layout);
        $this->layout->expects($this->any())->method('createBlock')
            ->with('Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab\Customers')->willReturn($this->block);
        $this->response->expects($this->once())->method('setBody')->willReturn(true);
        $this->block->expects($this->once())->method('toHtml')->willReturn(true);

        $model = new \Magento\Reminder\Controller\Adminhtml\Reminder\CustomerGrid(
            $this->context,
            $this->coreRegistry,
            $this->ruleFactory,
            $this->conditionFactory,
            $this->dataFilter
        );
        $model->execute();
    }
}
