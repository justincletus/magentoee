<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Model\Plugin;

class CmsPageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\VersionsCms\Model\Plugin\CmsPage
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\VersionsCms\Model\Plugin\CmsPage();
    }

    public function testAfterGetAvailableStatuses()
    {
        $subjectMock = $this->getMock('Magento\Cms\Model\Page', [], [], '', false);
        $result = $this->_model->afterGetAvailableStatuses($subjectMock, []);
        $this->assertTrue(isset($result[\Magento\Cms\Model\Page::STATUS_ENABLED]));
        $this->assertEquals('Published', $result[\Magento\Cms\Model\Page::STATUS_ENABLED]);
    }
}
