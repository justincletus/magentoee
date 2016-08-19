<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\PersistentHistory\Test\Unit\Plugin;

class CleanExpiredQuotesPluginTest extends \PHPUnit_Framework_TestCase
{
    public function testBeforeExecute()
    {
        $plugin = new \Magento\PersistentHistory\Plugin\CleanExpiredQuotesPlugin();
        $subjectMock = $this->getMock(
            'Magento\Sales\Cron\CleanExpiredQuotes',
            ['setExpireQuotesAdditionalFilterFields'],
            [],
            '',
            false);

        $subjectMock->expects($this->once())
            ->method('setExpireQuotesAdditionalFilterFields')
            ->with(['is_persistent' => 0])
            ->willReturn(null);

        $this->assertNull($plugin->beforeExecute($subjectMock));
    }
}
