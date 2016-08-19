<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\MultipleWishlist\Test\Unit\Model\Search\Strategy;

use Magento\MultipleWishlist\Model\Search\Strategy\Email;

class EmailTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    /** @var \Magento\Customer\Model\CustomerFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $customerFactory;

    /** @var \Magento\MultipleWishlist\Model\Search\Strategy\Email */
    protected $model;

    public function setUp()
    {
        $this->storeManager = $this->getMockForAbstractClass(
            'Magento\Store\Model\StoreManagerInterface',
            [],
            '',
            false
        );
        $this->customerFactory = $this->getMock('Magento\Customer\Model\CustomerFactory', ['create'], [], '', false);
        $this->model = new Email($this->customerFactory, $this->storeManager);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Please enter a valid email address.
     */
    public function testSetSearchParamsWithException()
    {
        $this->model->setSearchParams([]);
    }

    public function testFilterCollection()
    {
        $collection = $this->getMock('Magento\Wishlist\Model\ResourceModel\Wishlist\Collection', [], [], '', false);
        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $customer = $this->getMock(
            'Magento\Customer\Model\Customer',
            ['setWebsiteId', 'getId', 'loadByEmail'],
            [],
            '',
            false
        );
        $this->customerFactory->expects($this->once())
            ->method('create')
            ->willReturn($customer);
        $customer->expects($this->once())
            ->method('setWebsiteId')
            ->willReturnSelf();
        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->willReturn($store);
        $store->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(1);
        $customer->expects($this->once())
            ->method('loadByEmail')
            ->willReturnSelf();
        $customer->expects($this->once())
            ->method('getId')
            ->willReturn(23);

        $wishlist = $this->getMock('Magento\Wishlist\Model\Wishlist', ['setCustomer'], [], '', false);
        $wishlist->expects($this->once())
            ->method('setCustomer')
            ->with($customer);
        $iterator = new \ArrayObject([$wishlist]);
        $collection->expects($this->once())
            ->method('filterByCustomerId')
            ->with(23);
        $collection->expects($this->once())
            ->method('getIterator')
            ->willReturn($iterator);

        $this->assertSame($collection, $this->model->filterCollection($collection));
    }
}
