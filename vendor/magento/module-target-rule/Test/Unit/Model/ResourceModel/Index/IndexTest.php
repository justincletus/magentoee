<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TargetRule\Test\Unit\Model\ResourceModel\Index;

use Magento\TargetRule\Model\ResourceModel\Index\Index;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Index
     */
    private $model;

    /**
     * @var Magento\TargetRule\Model\Cache\Index|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    /**
     * @var int
     */
    private $type = 1;

    protected function setUp()
    {
        $this->cache = $this->getMockBuilder('Magento\TargetRule\Model\Cache\Index')
            ->disableOriginalConstructor()
            ->getMock();
        $this->cache->expects($this->any())
            ->method('getTag')
            ->willReturn('target_rule');

        $this->model = (new ObjectManager($this))->getObject(
            'Magento\TargetRule\Model\ResourceModel\Index\Index',
            ['cache' => $this->cache, 'type' => $this->type]
        );
    }

    public function testLoadProductIdsBySegmentIdNotArray()
    {
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $product->expects($this->once())
            ->method('getEntityId')
            ->willReturn(2);

        /** @var \Magento\TargetRule\Model\Index|\PHPUnit_Framework_MockObject_MockObject $indexModel */
        $indexModel = $this->getMockBuilder('Magento\TargetRule\Model\Index')
            ->disableOriginalConstructor()
            ->getMock();
        $indexModel->expects($this->once())
            ->method('getProduct')
            ->willReturn($product);
        $indexModel->expects($this->once())
            ->method('getStoreId')
            ->willReturn(3);
        $indexModel->expects($this->once())
            ->method('getCustomerGroupId')
            ->willReturn(5);

        $this->cache->expects($this->once())
            ->method('load')
            ->with('target_rule_1_2_3_5_352')
            ->willReturn(null);

        $result = $this->model->loadProductIdsBySegmentId($indexModel, 352);

        $this->assertEquals([], $result);
    }

    public function testLoadProductIdsBySegmentIdString()
    {
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $product->expects($this->once())
            ->method('getEntityId')
            ->willReturn(2);

        /** @var \Magento\TargetRule\Model\Index|\PHPUnit_Framework_MockObject_MockObject $indexModel */
        $indexModel = $this->getMockBuilder('Magento\TargetRule\Model\Index')
            ->disableOriginalConstructor()
            ->getMock();
        $indexModel->expects($this->once())
            ->method('getProduct')
            ->willReturn($product);
        $indexModel->expects($this->once())
            ->method('getStoreId')
            ->willReturn(3);
        $indexModel->expects($this->once())
            ->method('getCustomerGroupId')
            ->willReturn(5);

        $this->cache->expects($this->once())
            ->method('load')
            ->with('target_rule_1_2_3_5_352')
            ->willReturn('a:2:{i:0;i:7;i:1;i:9;}');

        $result = $this->model->loadProductIdsBySegmentId($indexModel, 352);

        $this->assertEquals([7, 9], $result);
    }

    public function testLoadProductIdsBySegmentIdArray()
    {
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $product->expects($this->once())
            ->method('getEntityId')
            ->willReturn(2);

        /** @var \Magento\TargetRule\Model\Index|\PHPUnit_Framework_MockObject_MockObject $indexModel */
        $indexModel = $this->getMockBuilder('Magento\TargetRule\Model\Index')
            ->disableOriginalConstructor()
            ->getMock();
        $indexModel->expects($this->once())
            ->method('getProduct')
            ->willReturn($product);
        $indexModel->expects($this->once())
            ->method('getStoreId')
            ->willReturn(3);
        $indexModel->expects($this->once())
            ->method('getCustomerGroupId')
            ->willReturn(5);

        $this->cache->expects($this->once())
            ->method('load')
            ->with('target_rule_1_2_3_5_352')
            ->willReturn([2, 5, 7]);

        $result = $this->model->loadProductIdsBySegmentId($indexModel, 352);

        $this->assertEquals([2, 5, 7], $result);
    }

    public function testSaveResultForCustomerSegmentsNotSave()
    {
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $product->expects($this->once())
            ->method('getEntityId')
            ->willReturn(2);

        /** @var \Magento\TargetRule\Model\Index|\PHPUnit_Framework_MockObject_MockObject $indexModel */
        $indexModel = $this->getMockBuilder('Magento\TargetRule\Model\Index')
            ->disableOriginalConstructor()
            ->getMock();
        $indexModel->expects($this->once())
            ->method('getProduct')
            ->willReturn($product);
        $indexModel->expects($this->once())
            ->method('getStoreId')
            ->willReturn(3);
        $indexModel->expects($this->once())
            ->method('getCustomerGroupId')
            ->willReturn(4);

        $this->cache->expects($this->once())
            ->method('save')
            ->with(
                'a:2:{i:0;i:7;i:1;i:9;}',
                'target_rule_1_2_3_4_32',
                [
                    'target_rule_1_product_7',
                    'target_rule_1_product_9',
                    'target_rule_1_main_entity_2',
                    'target_rule_1_main_store_3',
                    'target_rule_1_main_customer_group_4',
                    'target_rule_1_main_customer_segment_32'
                ]
            )->willReturn(false);
        $this->cache->expects($this->once())
            ->method('remove')
            ->with('target_rule_1_2_3_4_32');

        $result = $this->model->saveResultForCustomerSegments($indexModel, 32, [7, 9]);

        $this->assertEquals($this->model, $result);
    }

    public function testSaveResultForCustomerSegmentsSave()
    {
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $product->expects($this->once())
            ->method('getEntityId')
            ->willReturn(2);

        /** @var \Magento\TargetRule\Model\Index|\PHPUnit_Framework_MockObject_MockObject $indexModel */
        $indexModel = $this->getMockBuilder('Magento\TargetRule\Model\Index')
            ->disableOriginalConstructor()
            ->getMock();
        $indexModel->expects($this->once())
            ->method('getProduct')
            ->willReturn($product);
        $indexModel->expects($this->once())
            ->method('getStoreId')
            ->willReturn(3);
        $indexModel->expects($this->once())
            ->method('getCustomerGroupId')
            ->willReturn(4);

        $this->cache->expects($this->once())
            ->method('save')
            ->with(
                'a:2:{i:0;i:7;i:1;i:9;}',
                'target_rule_1_2_3_4_32',
                [
                    'target_rule_1_product_7',
                    'target_rule_1_product_9',
                    'target_rule_1_main_entity_2',
                    'target_rule_1_main_store_3',
                    'target_rule_1_main_customer_group_4',
                    'target_rule_1_main_customer_segment_32'
                ]
            )->willReturn(true);

        $result = $this->model->saveResultForCustomerSegments($indexModel, 32, [7, 9]);

        $this->assertEquals($this->model, $result);
    }

    public function testSaveResultForCustomerSegmentsEmpty()
    {
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $product */
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();
        $product->expects($this->once())
            ->method('getEntityId')
            ->willReturn(2);

        /** @var \Magento\TargetRule\Model\Index|\PHPUnit_Framework_MockObject_MockObject $indexModel */
        $indexModel = $this->getMockBuilder('Magento\TargetRule\Model\Index')
            ->disableOriginalConstructor()
            ->getMock();
        $indexModel->expects($this->once())
            ->method('getProduct')
            ->willReturn($product);
        $indexModel->expects($this->once())
            ->method('getStoreId')
            ->willReturn(3);
        $indexModel->expects($this->once())
            ->method('getCustomerGroupId')
            ->willReturn(4);

        $this->cache->expects($this->once())
            ->method('remove')
            ->with('target_rule_1_2_3_4_32');

        $result = $this->model->saveResultForCustomerSegments($indexModel, 32, []);

        $this->assertEquals($this->model, $result);
    }

    public function testCleanIndexArray()
    {
        $this->cache->expects($this->once())
            ->method('clean')
            ->with(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['target_rule_1_main_store_423']);

        $result = $this->model->cleanIndex([423]);

        $this->assertEquals($this->model, $result);
    }

    public function testCleanIndexObject()
    {
        /** @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject $store */
        $store = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();
        $store->expects($this->once())
            ->method('getId')
            ->willReturn(323);

        $this->cache->expects($this->once())
            ->method('clean')
            ->with(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['target_rule_1_main_store_323']);

        $result = $this->model->cleanIndex($store);

        $this->assertEquals($this->model, $result);
    }

    public function testCleanIndexNull()
    {
        $this->cache->expects($this->once())
            ->method('clean')
            ->with(\Zend_Cache::CLEANING_MODE_ALL);

        $result = $this->model->cleanIndex(null);

        $this->assertEquals($this->model, $result);
    }

    public function testDeleteProductFromIndex()
    {
        $this->cache->expects($this->once())
            ->method('clean')
            ->with(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['target_rule_1_main_entity_3', 'target_rule_1_product_3']);

        $result = $this->model->deleteProductFromIndex(3);

        $this->assertEquals($this->model, $result);
    }

    public function testDeleteProductFromIndexNull()
    {
        $result = $this->model->deleteProductFromIndex(null);

        $this->assertEquals($this->model, $result);
    }
}
