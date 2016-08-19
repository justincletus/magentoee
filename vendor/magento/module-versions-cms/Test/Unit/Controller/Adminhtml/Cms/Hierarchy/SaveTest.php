<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\VersionsCms\Test\Unit\Controller\Adminhtml\Cms\Hierarchy;

class SaveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $jsonHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $node;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $session;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $actionFlag;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManager;

    /**
     * @var \Magento\VersionsCms\Controller\Adminhtml\Cms\Hierarchy\Save
     */
    protected $saveController;

    protected function setUp()
    {
        $this->request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->jsonHelper = $this->getMock('Magento\Framework\Json\Helper\Data', [], [], '', false);
        $this->node = $this->getMock('Magento\VersionsCms\Model\Hierarchy\Node', [], [], '', false);
        $this->node->expects($this->once())->method('collectTree');
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $this->response = $this->getMock(
            'Magento\Framework\App\ResponseInterface',
            ['setRedirect', 'sendResponse'],
            [],
            '',
            false
        );
        $this->messageManager = $this->getMock('\Magento\Framework\Message\Manager', [], [], '', false);;
        $this->session = $this->getMock('Magento\Backend\Model\Session', ['setIsUrlNotice'], [], '', false);
        $this->actionFlag = $this->getMock('Magento\Framework\App\ActionFlag', ['get'], [], '', false);
        $this->backendHelper = $this->getMock('\Magento\Backend\Helper\Data', ['getUrl'], [], '', false);
        $this->context = $this->getMock('Magento\Backend\App\Action\Context', [], [], '', false);
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->saveController = $objectManager->getObject(
            'Magento\VersionsCms\Controller\Adminhtml\Cms\Hierarchy\Save',
            [
                'request' => $this->request,
                'response' => $this->response,
                'helper' => $this->backendHelper,
                'objectManager' => $this->objectManagerMock,
                'session' => $this->session,
                'actionFlag' => $this->actionFlag,
                'messageManager' => $this->messageManager
            ]
        );
    }

    /**
     * @param int $nodesDataEncoded
     * @param array $nodesData
     * @param array $post
     * @param string $path
     */
    protected function prepareTests($nodesDataEncoded, $nodesData, $post, $path)
    {
        $this->request->expects($this->atLeastOnce())->method('isPost')->willReturn(true);
        $this->request->expects($this->atLeastOnce())->method('getPostValue')->willReturn($post);

        $this->jsonHelper->expects($this->once())
            ->method('jsonDecode')
            ->with($nodesDataEncoded)
            ->willReturn($nodesData);

        $this->node->expects($this->once())->method('collectTree')->with($nodesData, []);

        $this->objectManagerMock->expects($this->atLeastOnce())
            ->method('create')
            ->with('Magento\VersionsCms\Model\Hierarchy\Node')
            ->willReturn($this->node);
        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Framework\Json\Helper\Data')
            ->willReturn($this->jsonHelper);

        $this->response->expects($this->once())->method('setRedirect')->with($path);

        $this->session->expects($this->once())->method('setIsUrlNotice')->with(true);

        $this->actionFlag->expects($this->once())->method('get')->with('', 'check_url_settings')->willReturn(true);

        $this->backendHelper->expects($this->atLeastOnce())->method('getUrl')->with($path)->willReturn($path);
    }

    /**
     * @param int $nodesDataEncoded
     * @param array $nodesData
     * @param array $post
     * @param string $path
     *
     * @dataProvider successMessageDisplayedDataProvider
     */
    public function testSuccessMessageDisplayed($nodesDataEncoded, $nodesData, $post, $path)
    {
        $this->prepareTests($nodesDataEncoded, $nodesData, $post, $path);

        $this->messageManager->expects($this->once())->method('addSuccess')->with(__('You have saved the hierarchy.'));

        $this->saveController->execute();
    }

    /**
     * @param int $nodesDataEncoded
     * @param array $nodesData
     * @param array $post
     * @param string $path
     *
     * @dataProvider successMessageNotDisplayedDataProvider
     */
    public function testSuccessMessageNotDisplayed($nodesDataEncoded, $nodesData, $post, $path)
    {
        $this->prepareTests($nodesDataEncoded, $nodesData, $post, $path);

        $this->messageManager->expects($this->never())->method('addSuccess');

        $this->saveController->execute();
    }

    /**
     * @return array
     */
    public function successMessageDisplayedDataProvider()
    {
        return [
            [
                'nodesDataEncoded' => 1,
                'nodesData' => [
                    [
                        'node_id' => 0,
                        'label' => 'Trial node',
                        'identifier' => 'trial',
                        'meta_chapter' => 0,
                        'meta_section' => 0,
                    ],
                    [
                        'node_id' => 1,
                        'label' => 'Trial node 1',
                        'identifier' => 'trial1',
                        'meta_chapter' => 0,
                        'meta_section' => 0,
                    ]
                ],
                'post' => [
                    'nodes_data' => 1,
                ],
                'path' => 'adminhtml/*/index',
            ]
        ];
    }

    /**
     * @return array
     */
    public function successMessageNotDisplayedDataProvider()
    {
        return [
            [
                'nodesDataEncoded' => 1,
                'nodesData' => [],
                'post' => [
                    'nodes_data' => 1,
                ],
                'path' => 'adminhtml/*/index',
            ]
        ];
    }
}
