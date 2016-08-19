<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Test\Unit\Model\Report\Config;


class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \DOMDocument
     */
    protected $source;

    /**
     * @var \Magento\Support\Model\Report\Config\Converter
     */
    protected $converter;

    /**
     * @var string
     */
    protected $configDir;

    protected function setUp()
    {
        $this->source = new \DOMDocument();

        /** @var \Magento\Support\Model\Report\Config\Converter $converter */
        $this->converter = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))
            ->getObject('Magento\Support\Model\Report\Config\Converter');

        $this->configDir = realpath(__DIR__) . DIRECTORY_SEPARATOR . '_files/';
    }

    public function testConvertValidShouldReturnArray()
    {
        $expected = [
            'groups' => [
                'general' => [
                    'title' => __('General'),
                    'sections' => [
                        40 => 'Magento\Support\Model\Report\Group\General\VersionSection',
                        50 => 'Magento\Support\Model\Report\Group\General\DataCountSection'
                    ],
                    'priority' => 10,
                    'data' => [
                        'Magento\Support\Model\Report\Group\General\VersionSection' => [],
                        'Magento\Support\Model\Report\Group\General\DataCountSection' => []
                    ]
                ],
                'environment' => [
                    'title' => __('Environment'),
                    'sections' => [
                        410 => 'Magento\Support\Model\Report\Group\Environment\EnvironmentSection'
                    ],
                    'priority' => 30,
                    'data' => [
                        'Magento\Support\Model\Report\Group\Environment\EnvironmentSection' => []
                    ]
                ]
            ]
        ];
        $this->source->load($this->configDir . 'report_valid.xml');
        $this->assertEquals($expected, $this->converter->convert($this->source));
    }

    /**
     * @param string $file
     * @param string $message
     * @dataProvider testConvertInvalidArgumentsDataProvider
     */
    public function testConvertInvalidArgumentsShouldThrowException($file, $message)
    {
        $this->setExpectedException('InvalidArgumentException', $message);
        $this->source->load($this->configDir . $file);
        $this->converter->convert($this->source);
    }

    /**
     * @return array
     */
    public function testConvertInvalidArgumentsDataProvider()
    {
        return [
            ['report_absent_name.xml', 'Attribute "name" of one of "group"s does not exist'],
            ['report_absent_sections.xml', 'Tag "sections" of one of "group"s does not exist']
        ];
    }
}
