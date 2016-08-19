<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Solr\Test\Unit\SearchAdapter;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class QueryFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Solr\SearchAdapter\QueryFactory
     */
    private $factory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManager;

    private $instanceName = '\StdClass';

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->objectManager = $this->getMock('Magento\Framework\ObjectManagerInterface');

        $this->factory = $helper->getObject(
            'Magento\Solr\SearchAdapter\QueryFactory',
            [
                'objectManager' => $this->objectManager,
                'instanceName' => $this->instanceName,
            ]
        );
    }

    public function testCreate()
    {
        $result = $this->factory->create();
        $this->assertInstanceOf($this->instanceName, $result);
    }
}
