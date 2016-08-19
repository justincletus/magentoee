<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report;

class DataProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetData()
    {
        $groupNames = ['general', 'environment'];
        $expected = [null => ['general' => ['report_groups' => $groupNames]]];

        $config = $this->getMock('Magento\Support\Model\Report\Config', [], [], '', false);
        $config->expects($this->once())->method('getGroupNames')->willReturn($groupNames);

        /** @var \Magento\Support\Model\Report\DataProvider $dataProvider */
        $dataProvider = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))
            ->getObject('Magento\Support\Model\Report\DataProvider', ['reportConfig' => $config]);

        $this->assertEquals($expected, $dataProvider->getData());
    }
}
