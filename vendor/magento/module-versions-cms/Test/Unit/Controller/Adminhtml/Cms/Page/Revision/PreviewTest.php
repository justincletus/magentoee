<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Controller\Adminhtml\Cms\Page\Revision;

/**
 * Class PreviewTest
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PreviewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Store\Model\Store[]|\PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $stores;

    /**
     * @var \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision\Preview
     */
    protected $controller;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $session;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cookieMetadata;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Session\Config\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionConfig;

    /**
     * @var \Magento\Backend\App\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $store;

    /**
     * @var \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactory;

    /**
     * @var \Magento\VersionsCms\Model\PageLoader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageLoader;

    /**
     * @var \Magento\Cms\Model\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $page;

    /**
     * @var \Magento\Framework\View\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    /**
     * @var \Magento\Backend\Block\Store\Switcher|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeSwitcher;

    /**
     * @var \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Preview\Form|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $previewForm;

    /**
     * @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageTitle;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $this->storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->config = $this->getMock('Magento\Backend\App\ConfigInterface');
        $this->sessionConfig = $this->getMock('Magento\Framework\Session\Config\ConfigInterface');
        $this->cookieManager = $this->getMock('Magento\Framework\Stdlib\CookieManagerInterface');
        $this->cookieMetadataFactory = $this->getMock(
            'Magento\Framework\Stdlib\Cookie\CookieMetadataFactory',
            [],
            [],
            '',
            false
        );
        $this->cookieMetadata = $this->getMock(
            'Magento\Framework\Stdlib\Cookie\PublicCookieMetadata',
            [],
            [],
            '',
            false
        );
        $this->session = $this->getMock('Magento\Backend\Model\Session', [], [], '', false);
        $this->request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->resultFactory = $this->getMock('Magento\Framework\Controller\ResultFactory', [], [], '', false);

        $contextArguments = $this->objectManagerHelper->getConstructArguments('Magento\Backend\App\Action\Context');
        $contextArguments['session'] = $this->session;
        $contextArguments['request'] = $this->request;
        $contextArguments['resultFactory'] = $this->resultFactory;
        $this->context = $this->objectManagerHelper->getObject('Magento\Backend\App\Action\Context', $contextArguments);

        $this->pageLoader = $this->getMock('Magento\VersionsCms\Model\PageLoader', [], [], '', false);
        $this->page = $this->getMock('Magento\Cms\Model\Page', ['getUnderVersionControl'], [], '', false);
        $this->layout = $this->getMock('Magento\Framework\View\Layout', [], [], '', false);
        $this->storeSwitcher = $this->getMock('Magento\Backend\Block\Store\Switcher', [], [], '', false);
        $this->previewForm = $this->getMock(
            'Magento\VersionsCms\Block\Adminhtml\Cms\Page\Preview\Form',
            [],
            [],
            '',
            false
        );
        $this->pageConfig = $this->getMock('Magento\Framework\View\Page\Config', [], [], '', false);
        $this->pageTitle = $this->getMock('Magento\Framework\View\Page\Title', [], [], '', false);

        /** @var \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision\Preview $controller */
        $this->controller = $this->objectManagerHelper->getObject(
            'Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision\Preview',
            [
                'storeManager' => $this->storeManager,
                'config' => $this->config,
                'sessionConfig' => $this->sessionConfig,
                'context' => $this->context,
                'cookieMetadataFactory' => $this->cookieMetadataFactory,
                'cookieManager' => $this->cookieManager,
                'pageLoader' => $this->pageLoader
            ]
        );
        $this->stores = [$this->store];
    }

    /**
     * @param array $sessionCookieData
     * @param string|null $cookieValue
     * @param array $postData
     * @param string $expectedResultType
     * @param string $expectedResultClass
     * @param int $setCookie
     * @param int $showPage
     * @param bool $versionControl
     * @dataProvider executeDataProvider
     */
    public function testExecute(
        array $sessionCookieData,
        $cookieValue,
        array $postData,
        $expectedResultType,
        $expectedResultClass,
        $setCookie,
        $showPage,
        $versionControl
    ) {
        $this->checkSessionCookie($sessionCookieData, $cookieValue, $setCookie);

        $this->checkRenderPageOrForwardToNoroute(
            $postData,
            $expectedResultType,
            $expectedResultClass,
            $showPage,
            $versionControl
        );
    }

    /**
     * @return array
     */
    public function executeDataProvider()
    {
        $cookieDomain = 'localhost';
        $basePath = '/index.php/';

        $sessionCookieData = [
            'cookie_domain' => $cookieDomain,
            'base_path' => $basePath,
            'url' => 'http://' . $cookieDomain . $basePath,
            'session_life_time' => 3600,
            'config_cookie_path' => '/',
            'cookie_secure' => false,
            'cookie_http_only' => true,
            'session_name' => 'admin',
            'cookie_path' => $basePath . \Magento\VersionsCms\Model\Page\Revision::PREVIEW_URI
        ];

        $nonEmptyPost = [
            'page_id' => 1,
            'stores' => [1, 2, 3]
        ];

        $emptyPost = [
            'page_id' => null
        ];

        return [
            'empty_post_but_cookie_exists_should_forward_to_no_route' => [
                $sessionCookieData,
                'test_cookie_value',
                $emptyPost,
                \Magento\Framework\Controller\ResultFactory::TYPE_FORWARD,
                'Magento\Backend\Model\View\Result\Forward',
                'setCookie' => 0,
                'showPage' => 0,
                'versionControl' => 0
            ],
            'empty_post_and_empty_cookie_should_forward_to_no_route' => [
                $sessionCookieData,
                '',
                $emptyPost,
                \Magento\Framework\Controller\ResultFactory::TYPE_FORWARD,
                'Magento\Backend\Model\View\Result\Forward',
                'setCookie' => 0,
                'showPage' => 0,
                'versionControl' => 0
            ],
            'page_id_provided_and_cookie_exist_should_render_page' => [
                $sessionCookieData,
                'test_cookie_value',
                $nonEmptyPost,
                \Magento\Framework\Controller\ResultFactory::TYPE_PAGE,
                'Magento\Backend\Model\View\Result\Page',
                'setCookie' => 1,
                'showPage' => 1,
                'versionControl' => 1,
            ]
        ];
    }

    /**
     * Perform session and cookies setters and getters assertions
     *
     * @param array $sessionCookieData
     * @param string|null $cookieValue
     * @param int $setCookie
     */
    protected function checkSessionCookie(array $sessionCookieData, $cookieValue, $setCookie)
    {
        $this->store->expects($this->exactly($setCookie))->method('getBaseUrl')->willReturn($sessionCookieData['url']);

        $this->storeManager->expects($this->exactly($setCookie))->method('getStores')->willReturn($this->stores);

        $this->config->expects($this->any())->method('getValue')
            ->willReturnMap(
                [
                    [
                        \Magento\Backend\Model\Auth\Session::XML_PATH_SESSION_LIFETIME,
                        $sessionCookieData['session_life_time']
                    ]
                ]
            );

        $this->sessionConfig->expects($this->exactly($setCookie))->method('getCookiePath')
            ->willReturn($sessionCookieData['config_cookie_path']);
        $this->sessionConfig->expects($this->exactly($setCookie))->method('getCookieDomain')
            ->willReturn($sessionCookieData['cookie_domain']);
        $this->sessionConfig->expects($this->exactly($setCookie))->method('getCookieSecure')
            ->willReturn($sessionCookieData['cookie_secure']);
        $this->sessionConfig->expects($this->exactly($setCookie))->method('getCookieHttpOnly')
            ->willReturn($sessionCookieData['cookie_http_only']);

        $this->cookieManager->expects($this->exactly($setCookie))->method('getCookie')
            ->with($sessionCookieData['session_name'])
            ->willReturn($cookieValue);

        $this->cookieMetadata->expects($this->exactly($setCookie))->method('setDuration')
            ->with($sessionCookieData['session_life_time'])
            ->willReturnSelf();
        $this->cookieMetadata->expects($this->exactly($setCookie))->method('setPath')
            ->with($sessionCookieData['cookie_path'])
            ->willReturnSelf();
        $this->cookieMetadata->expects($this->exactly($setCookie))->method('setDomain')
            ->with($sessionCookieData['cookie_domain'])
            ->willReturnSelf();
        $this->cookieMetadata->expects($this->exactly($setCookie))->method('setSecure')
            ->with($sessionCookieData['cookie_secure'])
            ->willReturnSelf();
        $this->cookieMetadata->expects($this->exactly($setCookie))->method('setHttpOnly')
            ->with($sessionCookieData['cookie_http_only'])
            ->willReturnSelf();

        $this->cookieManager->expects($this->exactly($setCookie))->method('setPublicCookie')
            ->with($sessionCookieData['session_name'], $cookieValue, $this->cookieMetadata);

        $this->cookieMetadataFactory->expects($this->exactly($setCookie))->method('createPublicCookieMetadata')
            ->willReturn($this->cookieMetadata);

        $this->session->expects($this->exactly($setCookie))->method('getName')
            ->willReturn($sessionCookieData['session_name']);
    }

    /**
     * Perform assertions on page rendering or routing to 'noroute'
     *
     * @param array $postData
     * @param string $expectedResultType
     * @param string $expectedResultClass
     * @param int $showPage
     * @param int $versionControl
     */
    protected function checkRenderPageOrForwardToNoroute(
        array $postData,
        $expectedResultType,
        $expectedResultClass,
        $showPage,
        $versionControl
    ) {
        $this->layout->expects($this->any())->method('getBlock')
            ->willReturnMap(
                [
                    ['store_switcher', $this->storeSwitcher],
                    ['preview_form', $this->previewForm]
                ]
            );

        $this->pageTitle->expects($this->exactly($showPage))->method('prepend')
            ->with(__('Pages'));
        $this->pageConfig->expects($this->exactly($showPage))->method('getTitle')->willReturn($this->pageTitle);

        $expectedResult = $this->getMock($expectedResultClass, [], [], '', false);
        $expectedResult->expects($this->any())->method('getLayout')->willReturn($this->layout);
        $expectedResult->expects($this->exactly($showPage))->method('getConfig')->willReturn($this->pageConfig);
        $expectedResult->expects($this->exactly((int)!$showPage))->method('forward')->with('noroute');

        $this->resultFactory->expects($this->once())->method('create')
            ->with($expectedResultType)
            ->willReturn($expectedResult);

        $this->request->expects($this->once())->method('getPostValue')->willReturn($postData);
        $this->request->expects($this->exactly($showPage))->method('getParam')->willReturn($postData['page_id']);

        $this->pageLoader->expects($this->exactly($showPage))->method('load')->with($postData['page_id'])
            ->willReturn($this->page);

        $this->page->expects($this->exactly($showPage))->method('getUnderVersionControl')->willReturn($versionControl);
        $this->layout->expects($this->exactly((int)(!$versionControl && $showPage)))->method('unsetChild')
            ->with('tools', 'revision_switcher');

        $this->assertSame($expectedResult, $this->controller->execute());
    }
}
