<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Model\Report\Group\Cron;

/**
 * All Configurable Cron Jobs
 */
class AllConfigurableCronJobsSection extends AbstractCronJobsSection
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $data = $this->prepareCronList($this->cronJobs->getAllConfigurableCronJobs());
        return $this->getReportData(__('All Configurable Cron Jobs'), $data);
    }
}
