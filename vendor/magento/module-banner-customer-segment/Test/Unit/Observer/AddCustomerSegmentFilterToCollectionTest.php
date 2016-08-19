<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\BannerCustomerSegment\Test\Unit\Observer;

use Magento\BannerCustomerSegment\Observer\AddCustomerSegmentFilterToCollection;

class AddCustomerSegmentFilterToCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Magento\BannerCustomerSegment\Observer\AddCustomerSegmentFilterToCollection
     */
    private $addCustomerSegmentFilterToCollectionObserver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_bannerSegmentLink;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_segmentCustomer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_segmentHelper;

    /**
     * @var \Magento\Framework\DB\Select
     */
    private $_select;

    protected function setUp()
    {
        $this->_bannerSegmentLink = $this->getMock(
            'Magento\BannerCustomerSegment\Model\ResourceModel\BannerSegmentLink',
            ['loadBannerSegments', 'saveBannerSegments', 'addBannerSegmentFilter', '__wakeup'],
            [],
            '',
            false
        );
        $this->_segmentCustomer = $this->getMock(
            'Magento\CustomerSegment\Model\Customer',
            ['getCurrentCustomerSegmentIds', '__wakeup'],
            [],
            '',
            false
        );
        $this->_segmentHelper = $this->getMock(
            'Magento\CustomerSegment\Helper\Data',
            ['isEnabled', 'addSegmentFieldsToForm'],
            [],
            '',
            false
        );

        $this->addCustomerSegmentFilterToCollectionObserver = new AddCustomerSegmentFilterToCollection(
            $this->_segmentHelper,
            $this->_bannerSegmentLink,
            $this->_segmentCustomer
        );

        $this->_select = new \Magento\Framework\DB\Select(
            $this->getMockForAbstractClass('Magento\Framework\DB\Adapter\Pdo\Mysql', [], '', false)
        );
    }

    protected function tearDown()
    {
        $this->_bannerSegmentLink = null;
        $this->_segmentCustomer = null;
        $this->_segmentHelper = null;
        $this->_select = null;
        $this->addCustomerSegmentFilterToCollectionObserver = null;
    }

    protected function _setFixtureSegmentIds(array $segmentIds)
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $this->_segmentCustomer->expects(
            $this->once()
        )->method(
            'getCurrentCustomerSegmentIds'
        )->will(
            $this->returnValue($segmentIds)
        );
    }

    /**
     * @dataProvider addCustomerSegmentFilterDataProvider
     * @param array $segmentIds
     */
    public function testAddCustomerSegmentFilterToCollection(array $segmentIds)
    {
        $this->_setFixtureSegmentIds($segmentIds);

        $this->_bannerSegmentLink->expects(
            $this->once()
        )->method(
            'addBannerSegmentFilter'
        )->with(
            $this->_select,
            $segmentIds
        );

        $this->addCustomerSegmentFilterToCollectionObserver->execute(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\DataObject(
                        ['collection' => new \Magento\Framework\DataObject(['select' => $this->_select])]
                    ),
                ]
            )
        );
    }

    /**
     * @return array
     */
    public function addCustomerSegmentFilterDataProvider()
    {
        return ['segments' => [[123, 456]], 'no segments' => [[]]];
    }

    public function testAddCustomerSegmentFilterToCollectionDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $this->_segmentCustomer->expects($this->never())->method('getCurrentCustomerSegmentIds');
        $this->_bannerSegmentLink->expects($this->never())->method('addBannerSegmentFilter');

        $this->addCustomerSegmentFilterToCollectionObserver->execute(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\DataObject(
                        ['collection' => new \Magento\Framework\DataObject(['select' => $this->_select])]
                    ),
                ]
            )
        );
    }
}
