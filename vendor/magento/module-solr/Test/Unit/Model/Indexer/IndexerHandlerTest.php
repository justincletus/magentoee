<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\Test\Unit\Model\Indexer;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Solr\Model\Adminhtml\Source\IndexationMode;
use Magento\Solr\Model\Indexer\IndexerHandler;
use Magento\Store\Model\ScopeInterface;

class IndexerHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IndexerHandler
     */
    private $model;

    /**
     * @var \Magento\Solr\Model\Adapter\Solarium|\PHPUnit_Framework_MockObject_MockObject
     */
    private $adapter;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Indexer\SaveHandler\Batch|\PHPUnit_Framework_MockObject_MockObject
     */
    private $batch;

    protected function setUp()
    {
        $this->adapter = $this->getMockBuilder('Magento\Solr\Model\Adapter\Solarium')
            ->disableOriginalConstructor()
            ->getMock();

        $adapterFactory = $this->getMockBuilder('\Magento\Solr\Model\AdapterFactoryInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $adapterFactory->expects($this->any())
            ->method('createAdapter')
            ->willReturn($this->adapter);

        $this->scopeConfig = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->batch = $this->getMockBuilder('Magento\Framework\Indexer\SaveHandler\Batch')
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = (new ObjectManager($this))->getObject(
            'Magento\Solr\Model\Indexer\IndexerHandler',
            [
                'adapterFactory' => $adapterFactory,
                'scopeConfig' => $this->scopeConfig,
                'batch' => $this->batch,
            ]
        );
    }

    public function testCleanIndex()
    {
        $dimensionValue = 'SomeDimension';

        $dimension = $this->getMockBuilder('Magento\Framework\Search\Request\Dimension')
            ->disableOriginalConstructor()
            ->getMock();
        $dimension->expects($this->any())
            ->method('getValue')
            ->willReturn($dimensionValue);

        $this->adapter->expects($this->once())
            ->method('deleteDocs')
            ->with(["store_id:{$dimensionValue}"]);

        $result = $this->model->cleanIndex([$dimension]);

        $this->assertEquals($this->model, $result);
    }

    public function testIsAvailable()
    {
        $this->adapter->expects($this->once())
            ->method('ping')
            ->willReturn(true);

        $result = $this->model->isAvailable();

        $this->assertTrue($result);
    }

    public function testDeleteIndex()
    {
        $dimensionName = IndexerHandler::SCOPE_FIELD_NAME;
        $dimensionValue = 3;
        $uniqueKey = 'someUniqueKey';
        $documentId = 123;
        $query = $uniqueKey . ':' . $documentId . '|' . $dimensionValue;

        $dimension = $this->getMockBuilder('Magento\Framework\Search\Request\Dimension')
            ->disableOriginalConstructor()
            ->getMock();
        $dimension->expects($this->any())
            ->method('getName')
            ->willReturn($dimensionName);
        $dimension->expects($this->any())
            ->method('getValue')
            ->willReturn($dimensionValue);

        $this->adapter->expects($this->once())
            ->method('getUniqueKey')
            ->willReturn($uniqueKey);
        $this->adapter->expects($this->once())
            ->method('deleteDocs')
            ->with([$query]);

        $this->scopeConfig->expects($this->any())->method('getValue')
            ->with($this->anything(), ScopeInterface::SCOPE_STORE, $dimensionValue)
            ->willReturn(IndexationMode::MODE_PARTIAL);
        $result = $this->model->deleteIndex([$dimension], new \ArrayIterator([$documentId]));

        $this->assertEquals($this->model, $result);
    }

    public function testSaveIndex()
    {
        $dimensionName = IndexerHandler::SCOPE_FIELD_NAME;
        $dimensionValue = 3;
        $documentId = 123;
        $documents = new \ArrayIterator([$documentId]);

        $dimension = $this->getMockBuilder('Magento\Framework\Search\Request\Dimension')
            ->disableOriginalConstructor()
            ->getMock();
        $dimension->expects($this->once())
            ->method('getName')
            ->willReturn($dimensionName);
        $dimension->expects($this->any())
            ->method('getValue')
            ->willReturn($dimensionValue);

        $this->batch->expects($this->once())
            ->method('getItems')
            ->with($documents, 500)
            ->willReturn([[]]);

        $this->adapter->expects($this->once())
            ->method('prepareDocsPerStore')
            ->with([], $dimensionValue)
            ->willReturn([$documentId]);
        $this->adapter->expects($this->once())
            ->method('addDocs')
            ->with([$documentId]);
        $this->adapter->expects($this->once())
            ->method('holdCommit');

        $result = $this->model->saveIndex([$dimension], $documents);

        $this->assertEquals($this->model, $result);
    }
}
