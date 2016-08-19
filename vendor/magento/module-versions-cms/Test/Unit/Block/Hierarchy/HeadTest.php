<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Block\Hierarchy;

class HeadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\VersionsCms\Helper\Hierarchy|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cmsHierarchy;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\Node|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $chapter;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\Node|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $section;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\Node|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $next;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\Node|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $prev;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\Node|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $first;

    /**
     * @var \Magento\Framework\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\Node|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $node;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    /**
     * @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageConfig;

    /**
     * @var \Magento\VersionsCms\Block\Hierarchy\Head
     */
    protected $head;

    public function setUp()
    {
        $this->cmsHierarchy = $this->getMock('Magento\VersionsCms\Helper\Hierarchy', [], [], '', false);

        $this->chapter = $this->getMock('Magento\VersionsCms\Model\Hierarchy\Node', [], [], '', false);
        $this->section = $this->getMock('Magento\VersionsCms\Model\Hierarchy\Node', [], [], '', false);
        $this->next = $this->getMock('Magento\VersionsCms\Model\Hierarchy\Node', [], [], '', false);
        $this->prev = $this->getMock('Magento\VersionsCms\Model\Hierarchy\Node', [], [], '', false);
        $this->first = $this->getMock('Magento\VersionsCms\Model\Hierarchy\Node', [], [], '', false);

        $this->pageConfig = $this->getMock('Magento\Framework\View\Page\Config', [], [], '', false);
        $this->registry = $this->getMock('Magento\Framework\Registry', [], [], '', false);
        $this->node = $this->getMock('Magento\VersionsCms\Model\Hierarchy\Node', [], [], '', false);
        $this->layout = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);

        /** @var \Magento\VersionsCms\Block\Hierarchy\Head $head */
        $this->head = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))
            ->getObject(
                'Magento\VersionsCms\Block\Hierarchy\Head',
                [
                    'cmsHierarchy' => $this->cmsHierarchy,
                    'registry' => $this->registry,
                    'pageConfig' => $this->pageConfig
                ]
            );
    }

    public function testPrepareLayoutMetaDataEnabledAndNodeExistsShouldAddRemotePageAssets()
    {
        $chapterUrl = 'chapter/url';
        $sectionUrl = 'section/url';
        $nextUrl = 'next/url';
        $prevUrl = 'prev/url';
        $firstUrl = 'first/url';

        $treeMetaData = [
            'meta_cs_enabled' => true,
            'meta_next_previous' => true,
            'meta_first_last' => true
        ];

        $this->cmsHierarchy->expects($this->once())->method('isMetadataEnabled')->willReturn(true);

        $this->chapter->expects($this->once())->method('getId')->willReturn(1);
        $this->chapter->expects($this->once())->method('getUrl')->willReturn($chapterUrl);

        $this->section->expects($this->once())->method('getId')->willReturn(1);
        $this->section->expects($this->once())->method('getUrl')->willReturn($sectionUrl);

        $this->next->expects($this->once())->method('getId')->willReturn(1);
        $this->next->expects($this->once())->method('getUrl')->willReturn($nextUrl);

        $this->prev->expects($this->once())->method('getId')->willReturn(1);
        $this->prev->expects($this->once())->method('getUrl')->willReturn($prevUrl);

        $this->first->expects($this->once())->method('getId')->willReturn(1);
        $this->first->expects($this->once())->method('getUrl')->willReturn($firstUrl);

        $this->node->expects($this->once())->method('getTreeMetaData')->willReturn($treeMetaData);
        $this->node->expects($this->any())
            ->method('getMetaNodeByType')
            ->willReturnMap(
                [
                    [\Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_CHAPTER, $this->chapter],
                    [\Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_SECTION, $this->section],
                    [\Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_NEXT, $this->next],
                    [\Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_PREVIOUS, $this->prev],
                    [\Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_FIRST, $this->first],
                ]
            );

        $this->registry->expects($this->once())->method('registry')->with('current_cms_hierarchy_node')->willReturn(
            $this->node
        );

        $this->pageConfig->expects($this->at(0))->method('addRemotePageAsset')
            ->with(
                $chapterUrl,
                '',
                ['attributes' => ['rel' => \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_CHAPTER]]
            )
            ->willReturnSelf();
        $this->pageConfig->expects($this->at(1))->method('addRemotePageAsset')
            ->with(
                $sectionUrl,
                '',
                ['attributes' => ['rel' => \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_SECTION]]
            )
            ->willReturnSelf();
        $this->pageConfig->expects($this->at(2))->method('addRemotePageAsset')
            ->with(
                $nextUrl,
                '',
                ['attributes' => ['rel' => \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_NEXT]]
            )
            ->willReturnSelf();
        $this->pageConfig->expects($this->at(3))->method('addRemotePageAsset')
            ->with(
                $prevUrl,
                '',
                ['attributes' => ['rel' => \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_PREVIOUS]]
            )
            ->willReturnSelf();
        $this->pageConfig->expects($this->at(4))->method('addRemotePageAsset')
            ->with(
                $firstUrl,
                '',
                ['attributes' => ['rel' => \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_FIRST]]
            )
            ->willReturnSelf();

        $this->assertSame($this->head, $this->head->setLayout($this->layout));
    }
}
