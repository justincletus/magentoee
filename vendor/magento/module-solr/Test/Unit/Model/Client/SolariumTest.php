<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Solr\Test\Unit\Model\Client;

class SolariumTest extends \PHPUnit_Framework_TestCase
{

    /**
     * SUT
     *
     * @var \Magento\Solr\Model\Client\Solarium
     */
    protected $model;

    /**
     * @var \Solarium\Client|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $solariumClientMock;

    /**
     * @var \Solarium\Core\Client\Endpoint|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $endPointMock;

    protected function setUp()
    {
        $this->endPointMock = $this->getMockBuilder('\Solarium\Core\Client\Endpoint')
            ->setMethods(['setHost', 'setPort', 'setPath', 'setTimeout', 'setAuthentication'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->endPointMock->expects($this->any())->method('setHost')->willReturnSelf();
        $this->endPointMock->expects($this->any())->method('setPort')->willReturnSelf();
        $this->endPointMock->expects($this->any())->method('setPath')->willReturnSelf();
        $this->endPointMock->expects($this->any())->method('setTimeout')->willReturnSelf();
        $this->endPointMock->expects($this->any())->method('setAuthentication')->willReturnSelf();

        $this->solariumClientMock = $this->getMock(
            'Solarium\Client',
            [
                'getEndpoint',
                'createPing',
                'ping',
                'createSelect',
                'execute',
                'createUpdate',
                'update',
                'getPlugin'
            ],
            [],
            '',
            false
        );
        $this->solariumClientMock->expects($this->any())->method('getEndpoint')->willReturn($this->endPointMock);

        $this->model = new \Magento\Solr\Model\Client\Solarium($this->getOptions(), $this->solariumClientMock);
    }

    public function testConstructorOptions()
    {
        $options = $this->getOptions();

        // Validate 'path' param
        $this->endPointMock->expects($this->any())->method('setPath')->with('/' . $options['path'])->willReturnSelf();
        new \Magento\Solr\Model\Client\Solarium($options, $this->solariumClientMock);

        // Validate 'timeout' param
        $this->endPointMock->expects($this->once())->method('setTimeout')->willReturnSelf();
        new \Magento\Solr\Model\Client\Solarium($options, $this->solariumClientMock);

        $this->endPointMock->expects($this->never())->method('setTimeout')->willReturnSelf();
        unset($options['timeout']);
        new \Magento\Solr\Model\Client\Solarium($options, $this->solariumClientMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testConstructorOptionsException()
    {
        new \Magento\Solr\Model\Client\Solarium([]);
    }

    public function testPing()
    {
        $response = $this->getMock('\Solarium\QueryType\Ping\Result', ['getData'], [], '', false);
        $response->expects($this->once())->method('getData')->willReturn([]);
        $query = $this->getMock('\Solarium\QueryType\Ping\Query', [], [], '', false);
        $this->solariumClientMock->expects($this->once())->method('createPing')->willReturn($query);
        $this->solariumClientMock->expects($this->once())->method('ping')->willReturn($response);

        $this->assertEquals([], $this->model->ping());
    }

    public function testDeleteByQueries()
    {
        $rawQueries = '';
        $query = $this->getMock('\Solarium\QueryType\Update\Query\Query', ['addDeleteQueries'], [], '', false);
        $query->expects($this->once())->method('addDeleteQueries')->with($rawQueries);

        $response = $this->getMock('\Solarium\QueryType\Ping\Result', ['getResponse'], [], '', false);
        $response->expects($this->once())->method('getResponse')->willReturn([]);

        $this->solariumClientMock->expects($this->once())->method('createUpdate')->willReturn($query);
        $this->solariumClientMock->expects($this->once())->method('update')->with($query)->willReturn($response);
        $this->assertEquals([], $this->model->deleteByQueries($rawQueries));
    }

    public function testAddDocuments()
    {
        $docs = [];
        $query = $this->getMock('\Solarium\QueryType\Update\Query\Query', ['addDocuments'], [], '', false);
        $query->expects($this->once())->method('addDocuments')->with($docs);

        $response = $this->getMock('\Solarium\QueryType\Ping\Result', ['getResponse'], [], '', false);
        $response->expects($this->once())->method('getResponse')->willReturn([]);

        $this->solariumClientMock->expects($this->once())->method('createUpdate')->willReturn($query);
        $this->solariumClientMock->expects($this->once())->method('update')->with($query)->willReturn($response);
        $this->assertEquals([], $this->model->addDocuments($docs));
    }

    public function testOptimize()
    {
        $response = $this->getMock('\Solarium\QueryType\Update\Result', [], [], '', false);
        $query = $this->getMock('\Solarium\QueryType\Update\Query\Query', ['addOptimize'], [], '', false);
        $query->expects($this->once())->method('addOptimize');
        $this->solariumClientMock->expects($this->once())->method('createUpdate')->willReturn($query);
        $this->solariumClientMock->expects($this->once())->method('update')->with($query)->willReturn($response);
        $result = $this->model->optimize();
        $this->assertInstanceOf('Solarium\QueryType\Update\Result', $result);
    }

    public function testRollback()
    {
        $response = $this->getMock('\Solarium\QueryType\Update\Result', [], [], '', false);
        $query = $this->getMock('\Solarium\QueryType\Update\Query\Query', ['addRollback'], [], '', false);
        $query->expects($this->once())->method('addRollback');
        $this->solariumClientMock->expects($this->once())->method('createUpdate')->willReturn($query);
        $this->solariumClientMock->expects($this->once())->method('update')->with($query)->willReturn($response);
        $result = $this->model->rollback();
        $this->assertInstanceOf('Solarium\QueryType\Update\Result', $result);
    }

    public function testCommit()
    {
        $query = $this->getMock('\Solarium\QueryType\Update\Query\Query', ['addCommit'], [], '', false);
        $query->expects($this->once())->method('addCommit');
        $this->solariumClientMock->expects($this->once())->method('createUpdate')->willReturn($query);
        $this->solariumClientMock->expects($this->once())->method('update')->with($query);
        $this->model->commit();
    }

    /**
     * Get connection options
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            'hostname' => 'localhost',
            'login' => 'admin',
            'password' => 'pwd',
            'port' => '8985',
            'path' => 'mysolr',
            'timeout' => '5'
        ];
    }
}
