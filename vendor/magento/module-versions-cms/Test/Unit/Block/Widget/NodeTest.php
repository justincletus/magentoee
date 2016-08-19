<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Block\Widget;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\VersionsCms\Block\Widget\Node
     */
    protected $nodeWidget;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\Node|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $nodeMock;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\NodeFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $hierarchyNodeFactoryMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreRegistryMock;

    /**
     * @var string
     */
    protected $nodeLabel = 'Node Label';

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->storeMock = $this->getMockBuilder('Magento\Store\Model\Store')
            ->setMethods(['getId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->nodeMock = $this->getMockBuilder('Magento\VersionsCms\Model\Hierarchy\Node')
            ->disableOriginalConstructor()
            ->getMock();
        $this->hierarchyNodeFactoryMock = $this->getMockBuilder('Magento\VersionsCms\Model\Hierarchy\NodeFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->coreRegistryMock = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $storeManager = $this->getMockForAbstractClass('Magento\Store\Model\StoreManagerInterface');
        $storeManager->expects($this->any())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->nodeWidget = $objectManagerHelper->getObject(
            'Magento\VersionsCms\Block\Widget\Node',
            [
                'storeManager' => $storeManager,
                'registry' => $this->coreRegistryMock,
                'hierarchyNodeFactory' => $this->hierarchyNodeFactoryMock
            ]
        );
    }

    /**
     * @param int $storeId
     * @param array $data
     * @param string $value
     * @return void
     *
     * @dataProvider getLabelDataProvider
     */
    public function testGetLabel($storeId, $data, $value)
    {
        $this->initNodeMock();
        $this->storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($storeId);

        $this->nodeWidget->setData($data);
        $this->assertEquals($value, $this->nodeWidget->getLabel());
    }

    /**
     * @return array
     */
    public function getLabelDataProvider()
    {
        return [
            [
                $storeId = 1,
                $data = ['anchor_text_1' => 'value_1'],
                $value = 'value_1'
            ],
            [
                $storeId = 1,
                $data = ['anchor_text_1' => 'value_1', 'anchor_text_0' => 'value_0'],
                $value = 'value_1'
            ],
            [
                $storeId = 1,
                $data = ['anchor_text_1' => 'value_1', 'anchor_text_0' => 'value_0', 'anchor_text' => 'value'],
                $value = 'value_1'
            ],
            [
                $storeId = 1,
                $data = ['anchor_text_0' => 'value_0', 'anchor_text' => 'value'],
                $value = 'value_0'
            ],
            [
                $storeId = 1,
                $data = ['anchor_text_2' => 'value_2', 'anchor_text' => 'value'],
                $value = 'value'
            ],
            [
                'storeId' => 1,
                'data' => ['anchor_text' => null, 'anchor_text_1' => null],
                'value' => $this->nodeLabel
            ]
        ];
    }

    /**
     * @param int $storeId
     * @param array $data
     * @param string $value
     * @return void
     *
     * @dataProvider getTitleDataProvider
     */
    public function testGetTitle($storeId, $data, $value)
    {
        $nodeId = 1;
        $this->nodeWidget->setData(['node_id' => $nodeId]);
        $this->hierarchyNodeFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->nodeMock);
        $this->nodeMock->expects($this->once())
            ->method('load')
            ->with($nodeId)
            ->willReturnSelf();
        $this->nodeMock->expects($this->any())
            ->method('getLabel')
            ->willReturn($this->nodeLabel);
        $this->nodeWidget->toHtml();

        $this->storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($storeId);

        $this->nodeWidget->setData($data);
        $this->assertEquals($value, $this->nodeWidget->getTitle());
    }

    /**
     * @return array
     */
    public function getTitleDataProvider()
    {
        return [
            [
                $storeId = 1,
                $data = ['title_1' => 'value_1'],
                $value = 'value_1'
            ],
            [
                $storeId = 1,
                $data = ['title_1' => 'value_1', 'title_0' => 'value_0'],
                $value = 'value_1'
            ],
            [
                $storeId = 1,
                $data = ['title_1' => 'value_1', 'title_0' => 'value_0', 'title' => 'value'],
                $value = 'value_1'
            ],
            [
                $storeId = 1,
                $data = ['title_0' => 'value_0', 'title' => 'value'],
                $value = 'value_0'
            ],
            [
                $storeId = 1,
                $data = ['title_2' => 'value_2', 'title' => 'value'],
                $value = 'value'
            ],
            [
                'storeId' => 1,
                'data' => ['title' => null, 'title_1' => null],
                'value' => $this->nodeLabel
            ]
        ];
    }

    /**
     * @return void
     */
    public function testGetHref()
    {
        $url = 'http://localhost/';
        $this->initNodeMock();
        $this->nodeMock->expects($this->once())
            ->method('getUrl')
            ->willReturn($url);
        $this->assertSame($url, $this->nodeWidget->getHref());
    }

    /**
     * @param int $storeId
     * @param array $data
     * @param string $value
     * @return void
     *
     * @dataProvider getNodeIdDataProvider
     */
    public function testGetNodeId($storeId, $data, $value)
    {
        $this->storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($storeId);

        $this->nodeWidget->setData($data);
        $this->assertEquals($value, $this->nodeWidget->getNodeId());
    }

    /**
     * @return array
     */
    public function getNodeIdDataProvider()
    {
        return [
            [
                $storeId = 1,
                $data = ['node_id_1' => 'value_1'],
                $value = 'value_1'
            ],
            [
                $storeId = 1,
                $data = ['node_id_1' => 'value_1', 'node_id_0' => 'value_0'],
                $value = 'value_1'
            ],
            [
                $storeId = 1,
                $data = ['node_id_1' => 'value_1', 'node_id_0' => 'value_0', 'node_id' => 'value'],
                $value = 'value_1'
            ],
            [
                $storeId = 1,
                $data = ['node_id_0' => 'value_0', 'node_id' => 'value'],
                $value = 'value_0'
            ],
            [
                $storeId = 1,
                $data = ['node_id_2' => 'value_2', 'node_id' => 'value'],
                $value = 'value'
            ],
            [
                'storeId' => 1,
                'data' => ['node_id' => null, 'node_id_1' => null],
                'value' => false
            ]
        ];
    }

    /**
     * @return void
     */
    protected function initNodeMock()
    {
        $this->coreRegistryMock->expects($this->once())
            ->method('registry')
            ->with('current_cms_hierarchy_node')
            ->willReturn($this->nodeMock);
        $this->nodeMock->expects($this->any())
            ->method('getLabel')
            ->willReturn($this->nodeLabel);
        $this->nodeWidget->toHtml();
    }
}
