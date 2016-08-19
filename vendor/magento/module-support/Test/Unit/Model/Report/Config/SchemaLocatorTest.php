<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Config;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Support\Model\Report\Config\SchemaLocator
     */
    protected $schemaLocator;

    protected function setUp()
    {
        /** @var $objectManagerHelper */
        $objectManagerHelper = new ObjectManagerHelper($this);

        /** @var \Magento\Framework\Module\Dir\Reader|\PHPUnit_Framework_MockObject_MockObject $moduleReaderMock */
        $moduleReaderMock = $this->getMock('Magento\Framework\Module\Dir\Reader', [], [], '', false);
        $moduleReaderMock->expects($this->once())
            ->method('getModuleDir')
            ->with('etc', 'Magento_Support')
            ->willReturn('schema_dir');

        $this->schemaLocator = $objectManagerHelper->getObject(
            'Magento\Support\Model\Report\Config\SchemaLocator',
            [
                'moduleReader' => $moduleReaderMock
            ]
        );
    }

    public function testGetSchema()
    {
        $this->assertEquals('schema_dir/report.xsd', $this->schemaLocator->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $this->assertEquals(null, $this->schemaLocator->getPerFileSchema());
    }
}
