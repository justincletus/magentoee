<?php
/***
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\App\FrontController;

use Magento\VersionsCms\Model\Page\Revision;

class AdminSessionPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Session\AdminConfig|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adminConfig;

    /**
     * @var \Magento\Backend\Model\Auth\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $session;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\FrontController|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $frontController;

    /**
     * @var \Magento\VersionsCms\App\FrontController\AdminSessionPlugin
     */
    protected $plugin;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->adminConfig = $this->getMock('Magento\Backend\Model\Session\AdminConfig', [], [], '', false);
        $this->session = $this->getMock('Magento\Backend\Model\Auth\Session', [], [], '', false);
        $this->objectManager = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->frontController = $this->getMock('Magento\Framework\App\FrontController', [], [], '', false);

        $this->plugin = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))
            ->getObject(
                'Magento\VersionsCms\App\FrontController\AdminSessionPlugin',
                [
                    'request' => $this->request,
                    'objectManager' => $this->objectManager
                ]
            );
    }

    /**
     * Test before dispatch
     *
     * @param string $app
     * @param string $path
     * @param int $count
     * @dataProvider beforeDispatchDataProvider
     */
    public function testBeforeDispatch($app, $path, $count)
    {
        $this->objectManager->expects($this->exactly($count))->method('create')->willReturnMap(
            [
                ['Magento\Backend\Model\Session\AdminConfig', ['sessionName' => 'admin'], $this->adminConfig],
                ['Magento\Backend\Model\Auth\Session', ['sessionConfig' => $this->adminConfig], $this->session]
            ]
        );

        $this->request->expects($this->once())->method('getQuery')->with('app')->willReturn($app);
        $this->request->expects($this->once())->method('getRequestUri')->willReturn($path);
        $this->plugin->beforeDispatch($this->frontController);
    }

    /**
     * @return array
     */
    public function beforeDispatchDataProvider()
    {
        return [
            ['cms_preview', Revision::PREVIEW_URI, 2],
            ['cms_preview', '/other/uri', 0],
            ['setup', Revision::PREVIEW_URI, 0],
            ['setup', '/other/uri', 0],
        ];
    }
}
