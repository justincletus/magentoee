<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\GiftCard\Test\Unit\Pricing\Price;

class FinalPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCard\Pricing\Price\FinalPrice
     */
    protected $model;

    /**
     * @var \Magento\Catalog\Pricing\Price\BasePrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $basePriceMock;

    /**
     * @var \Magento\Framework\Pricing\SaleableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleableMock;

    /**
     * @var \Magento\Framework\Pricing\Adjustment\Calculator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $calculatorMock;
    /**
     * @var \Magento\Catalog\Pricing\Price\SpecialPrice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleableItemMock;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceCurrencyMock;

    /**
     * Set up function
     */
    public function setUp()
    {
        $this->saleableMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            [
                'getPriceInfo',
                'getGiftcardAmounts',
                '__wakeup'
            ],
            [],
            '',
            false
        );

        $this->basePriceMock = $this->getMock(
            'Magento\Catalog\Pricing\Price\BasePrice',
            [],
            [],
            '',
            false
        );

        $this->calculatorMock = $this->getMock(
            'Magento\Framework\Pricing\Adjustment\Calculator',
            [],
            [],
            '',
            false
        );

        $this->priceCurrencyMock = $this->getMock('\Magento\Framework\Pricing\PriceCurrencyInterface');
        $this->priceCurrencyMock->expects($this->any())
            ->method('convertAndRound')
            ->will($this->returnCallback(
                    function ($arg) {
                        return round(0.5 * $arg, 2);
                    }
                )
            );

        $this->model = new \Magento\GiftCard\Pricing\Price\FinalPrice(
            $this->saleableMock,
            1,
            $this->calculatorMock,
            $this->priceCurrencyMock
        );
    }

    /**
     * @param array $amounts
     * @param bool $expected
     *
     * @dataProvider getAmountsDataProvider
     */
    public function testGetAmounts($amounts, $expected)
    {
        $this->saleableMock->expects($this->any())
            ->method('getGiftcardAmounts')
            ->will($this->returnValue($amounts));

        $this->assertEquals($expected, $this->model->getAmounts());
    }

    /**
     * @return array
     */
    public function getAmountsDataProvider()
    {
        return [
            'one_amount' => [
                'amounts' => [
                    ['website_value' => 10.],
                ],
                'expected' => [5.],
            ],
            'two_amount' => [
                'amounts' => [
                    ['website_value' => 10.],
                    ['website_value' => 20.],
                ],
                'expected' => [5., 10.],
            ],
            'zero_amount' => [
                'amounts' => [],
                'expected' => [],
            ]

        ];
    }

    public function testGetAmountsCached()
    {
        $amount = [['website_value' => 5]];

        $this->saleableMock->expects($this->once())
            ->method('getGiftcardAmounts')
            ->will($this->returnValue($amount));

        $this->model->getAmounts();

        $this->assertEquals([2.5], $this->model->getAmounts());
    }

    /**
     * @param array $amounts
     * @param bool $expected
     *
     * @dataProvider getValueDataProvider
     */
    public function testGetValue($amounts, $expected)
    {
        $this->saleableMock->expects($this->any())
            ->method('getGiftcardAmounts')
            ->will($this->returnValue($amounts));

        $this->assertEquals($expected, $this->model->getValue());
    }

    /**
     * @return array
     */
    public function getValueDataProvider()
    {
        return [
            'one_amount' => [
                'amounts' => [
                    ['website_value' => 10.],
                ],
                'expected' => 5.,
            ],
            'two_amount' => [
                'amounts' => [
                    ['website_value' => 10.],
                    ['website_value' => 20.],
                ],
                'expected' => 5.,
            ],
            'zero_amount' => [
                'amounts' => [],
                'expected' => false,
            ]

        ];
    }
}
