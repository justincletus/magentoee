<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Group\Events;

use Magento\Framework\App\Area;

class AllFrontendEventsSectionTest extends AbstractEventsSectionTest
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
        return (string)__('All Frontend Events');
    }

    /**
     * {@inheritdoc}
     */
    protected function getSectionName()
    {
        return 'Magento\Support\Model\Report\Group\Events\AllFrontendEventsSection';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedAreaCode()
    {
        return Area::AREA_FRONTEND;
    }
}
