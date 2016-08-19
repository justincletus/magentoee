<?php
/***
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VersionsCms\Test\Unit\Block\Adminhtml\Cms\Page\Revision;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class EditTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Revision\Edit */
    private $model;

    /** @var  \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\Registry */
    private $coreRegistryMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\UrlInterface */
    private $urlBuilderMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject | \Magento\VersionsCms\Model\Page\Revision */
    private $revisionMock;

    public function setUp()
    {
        $this->coreRegistryMock = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->urlBuilderMock = $this->getMockBuilder('Magento\Framework\UrlInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->revisionMock = $this->getMockBuilder('Magento\VersionsCms\Model\Page\Revision')
            ->disableOriginalConstructor()
            ->getMock();
        $mocks = [
            'registry' => $this->coreRegistryMock,
            'urlBuilder' => $this->urlBuilderMock
        ];
        $this->model = (new ObjectManager($this))
            ->getObject('Magento\VersionsCms\Block\Adminhtml\Cms\Page\Revision\Edit', $mocks);
    }

    public function testGetBackUrl()
    {
        $pageId = '1';
        $versionId = '2';
        $params = [
            'page_id' => $pageId,
            'version_id' => $versionId
        ];
        $editVersionUrl = 'adminhtml/cms_page_version/edit';
        $backUrl = 'adminhtml/cms_page_version/edit/page_id/1/version_id/2/';

        $this->coreRegistryMock->expects($this->once())->method('registry')->willReturn($this->revisionMock);
        $this->revisionMock->expects($this->once())->method('getPageId')->willReturn($pageId);
        $this->revisionMock->expects($this->once())->method('getVersionId')->willReturn($versionId);
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with($editVersionUrl, $params)
            ->willReturn($backUrl);
        $this->assertSame($backUrl, $this->model->getBackUrl());
    }
}
