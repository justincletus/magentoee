<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Group\Events;

use Magento\Framework\App\Area;

class AllCrontabEventsSectionTest extends AbstractEventsSectionTest
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedType()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedTitle()
    {
        return (string)__('All Crontab Events');
    }

    /**
     * {@inheritdoc}
     */
    protected function getSectionName()
    {
        return 'Magento\Support\Model\Report\Group\Events\AllCrontabEventsSection';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedAreaCode()
    {
        return Area::AREA_CRONTAB;
    }
}
