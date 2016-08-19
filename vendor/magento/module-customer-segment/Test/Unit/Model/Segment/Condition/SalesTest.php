<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerSegment\Test\Unit\Model\Segment\Condition;

class SalesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Sales
     */
    protected $model;

    /**
     * @var \Magento\Rule\Model\Condition\Context
     */
    protected $context;

    /**
     * @var \Magento\CustomerSegment\Model\ResourceModel\Segment
     */
    protected $resourceSegment;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder('Magento\Rule\Model\Condition\Context')
            ->disableOriginalConstructor()
            ->getMock();

        $this->resourceSegment =
            $this->getMock('Magento\CustomerSegment\Model\ResourceModel\Segment', [], [], '', false);

        $this->model = new \Magento\CustomerSegment\Model\Segment\Condition\Sales(
            $this->context,
            $this->resourceSegment
        );
    }

    protected function tearDown()
    {
        unset(
            $this->model,
            $this->context,
            $this->resourceSegment
        );
    }

    public function testGetNewChildSelectOptions()
    {
        $data = [
            'value' => [
                [
                    'value' => 'Magento\CustomerSegment\Model\Segment\Condition\Order\Address',
                    'label' => __('Order Address'),
                ],
                [
                    'value' => 'Magento\CustomerSegment\Model\Segment\Condition\Sales\Salesamount',
                    'label' => __('Sales Amount'),
                ],
                [
                    'value' => 'Magento\CustomerSegment\Model\Segment\Condition\Sales\Ordersnumber',
                    'label' => __('Number of Orders'),
                ],
                [
                    'value' => 'Magento\CustomerSegment\Model\Segment\Condition\Sales\Purchasedquantity',
                    'label' => __('Purchased Quantity'),
                ],
            ],
            'label' => __('Sales'),
        ];

        $result = $this->model->getNewChildSelectOptions();

        $this->assertTrue(is_array($result));
        $this->assertEquals($data, $result);
    }
}
