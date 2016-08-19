<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Test\Unit\Model\Banner;

use Magento\Banner\Model\Config;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var int
     */
    const STORE_ID = 1;

    /**
     * @var \Magento\Banner\Model\Banner\Data
     */
    private $unit;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $bannerResource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $checkoutSession;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $httpContext;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $currentWebsite;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $banner;

    protected function setUp()
    {
        $this->bannerResource = $this->getMock('Magento\Banner\Model\ResourceModel\Banner', [], [], '', false);
        $this->checkoutSession = $this->getMock(
            'Magento\Checkout\Model\Session',
            ['getQuoteId', 'getQuote'],
            [],
            '',
            false
        );
        $this->httpContext = $this->getMock('\Magento\Framework\App\Http\Context');
        $this->currentWebsite = $this->getMock('Magento\Store\Model\Website', [], [], '', false);
        $this->banner = $this->getMock('Magento\Banner\Model\Banner', [], [], '', false);

        $pageFilterMock = $this->getMock('Magento\Cms\Model\Template\Filter', [], [], '', false);
        $pageFilterMock->expects($this->any())->method('filter')->will($this->returnArgument(0));
        $filterProviderMock = $this->getMock('Magento\Cms\Model\Template\FilterProvider', [], [], '', false);
        $filterProviderMock->expects($this->any())->method('getPageFilter')->will($this->returnValue($pageFilterMock));

        $currentStore = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $currentStore->expects($this->any())->method('getId')->willReturn(self::STORE_ID);
        $storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $storeManager->expects($this->once())->method('getStore')->will($this->returnValue($currentStore));
        $storeManager->expects($this->once())->method('getWebsite')->will($this->returnValue($this->currentWebsite));

        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->unit = $helper->getObject(
            'Magento\Banner\Model\Banner\Data',
            [
                'banner' => $this->banner,
                'bannerResource' => $this->bannerResource,
                'checkoutSession' => $this->checkoutSession,
                'httpContext' => $this->httpContext,
                'filterProvider' => $filterProviderMock,
                'storeManager' => $storeManager,
            ]
        );
    }

    /**
     * @param array $result
     * @return array
     */
    protected function getExpectedResult($result)
    {
        return [
            'items' => $result + [
                Config::BANNER_WIDGET_DISPLAY_SALESRULE => [],
                Config::BANNER_WIDGET_DISPLAY_CATALOGRULE => [],
                Config::BANNER_WIDGET_DISPLAY_FIXED => [],
            ]
        ];
    }

    public function testGetBannersContentFixed()
    {
        $this->bannerResource->expects($this->once())->method('getSalesRuleRelatedBannerIds')->willReturn([]);
        $this->bannerResource->expects($this->once())->method('getCatalogRuleRelatedBannerIds')->willReturn([]);
        $this->bannerResource->expects($this->once())->method('getActiveBannerIds')->willReturn([123]);


        $this->bannerResource->expects($this->any())->method('getStoreContent')
            ->with(123, self::STORE_ID)->willReturn('Fixed Banner 123');
        $this->banner->expects($this->any())->method('load')->with(123)->willReturnSelf();
        $this->banner->expects($this->any())->method('getTypes')->willReturn('footer');

        $this->assertEquals(
            $this->getExpectedResult([
                Config::BANNER_WIDGET_DISPLAY_FIXED => [
                    123 => [
                        'content' => 'Fixed Banner 123', 'types' => 'footer', 'id' => 123
                    ],
                ],
            ]),
            $this->unit->getSectionData()
        );
    }

    public function testGetBannersContentCatalogRule()
    {
        $this->httpContext->expects($this->any())->method('getValue')->willReturn('customer_group');
        $this->currentWebsite->expects($this->any())->method('getId')->willReturn('website_id');

        $this->bannerResource->expects($this->once())->method('getSalesRuleRelatedBannerIds')->willReturn([]);
        $this->bannerResource->expects($this->once())->method('getCatalogRuleRelatedBannerIds')
            ->with('website_id', 'customer_group')->willReturn([123]);
        $this->bannerResource->expects($this->once())->method('getActiveBannerIds')->willReturn([]);

        $this->bannerResource->expects($this->any())->method('getStoreContent')
            ->with(123, self::STORE_ID)->willReturn('CatalogRule Banner 123');
        $this->banner->expects($this->any())->method('load')->with(123)->willReturnSelf();
        $this->banner->expects($this->any())->method('getTypes')->willReturn('footer');

        $this->assertEquals(
            $this->getExpectedResult([
                Config::BANNER_WIDGET_DISPLAY_CATALOGRULE => [
                    123 => [
                        'content' => 'CatalogRule Banner 123', 'types' => 'footer', 'id' => 123
                    ],
                ],
            ]),
            $this->unit->getSectionData()
        );
    }

    public function testGetBannersContentSalesRule()
    {
        $quote = $this->getMock('\Magento\Quote\Model\Quote', ['getAppliedRuleIds'], [], '', false);
        $quote->expects($this->any())->method('getAppliedRuleIds')->willReturn('15,11,12');
        $this->checkoutSession->expects($this->once())->method('getQuoteId')->will($this->returnValue(8000));
        $this->checkoutSession->expects($this->once())->method('getQuote')->will($this->returnValue($quote));

        $this->bannerResource->expects($this->once())->method('getSalesRuleRelatedBannerIds')->with([15, 11, 12])
            ->willReturn([123]);
        $this->bannerResource->expects($this->once())->method('getCatalogRuleRelatedBannerIds')->willReturn([]);
        $this->bannerResource->expects($this->once())->method('getActiveBannerIds')->willReturn([]);

        $this->bannerResource->expects($this->any())->method('getStoreContent')
            ->with(123, self::STORE_ID)->willReturn('SalesRule Banner 123');
        $this->banner->expects($this->any())->method('load')->with(123)->willReturnSelf();
        $this->banner->expects($this->any())->method('getTypes')->willReturn('footer');

        $this->assertEquals(
            $this->getExpectedResult([
                Config::BANNER_WIDGET_DISPLAY_SALESRULE => [
                    123 => [
                        'content' => 'SalesRule Banner 123', 'types' => 'footer', 'id' => 123
                    ],
                ],
            ]),
            $this->unit->getSectionData()
        );
    }
}
