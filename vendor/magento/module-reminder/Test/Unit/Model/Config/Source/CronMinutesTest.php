<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reminder\Test\Unit\Model\Config\Source;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class CronMinutesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reminder\Model\Config\Source\CronMinutes
     */
    private $model;

    /**
     * @return void
     */
    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->model = $helper->getObject('Magento\Reminder\Model\Config\Source\CronMinutes');
    }

    /**
     * @return void
     */
    public function testGetCronMinutes()
    {
        $expected = [
            5 => __('5 minutes'),
            10 => __('10 minutes'),
            15 => __('15 minutes'),
            20 => __('20 minutes'),
            30 => __('30 minutes'),
        ];

        $this->assertEquals($expected, $this->model->getCronMinutes());
    }
}
