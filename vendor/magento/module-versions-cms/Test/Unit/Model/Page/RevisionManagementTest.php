<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Model\Page;

use Magento\VersionsCms\Model\Page\RevisionManagement;

/**
 * Test for Magento\VersionsCms\Model\Page\RevisionManagement
 */
class RevisionManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RevisionManagement
     */
    protected $revisionManagement;

    /**
     * @var \Magento\VersionsCms\Api\PageRevisionRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $revisionRepository;

    /**
     * Initialize revision management
     */
    public function setUp()
    {
        $this->revisionRepository = $this->getMockBuilder('Magento\VersionsCms\Api\PageRevisionRepositoryInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->revisionManagement = new RevisionManagement($this->revisionRepository);
    }

    /**
     * @test
     */
    public function testPublish()
    {
        $pageRevisionId = mt_rand(100, 999);
        $revisionMock = $this->getMockBuilder('Magento\VersionsCms\Model\Page\Revision')
            ->disableOriginalConstructor()
            ->getMock();
        $revisionMock->expects($this->once())->method('publish')->willReturnSelf();
        $this->revisionRepository->expects($this->once())
            ->method('getById')
            ->with($pageRevisionId)
            ->willReturn($revisionMock);
        $this->assertInstanceOf(
            'Magento\VersionsCms\Api\Data\PageRevisionInterface',
            $this->revisionManagement->publish($pageRevisionId)
        );
    }
}
