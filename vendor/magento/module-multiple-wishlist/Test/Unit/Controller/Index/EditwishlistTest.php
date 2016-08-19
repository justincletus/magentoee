<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\MultipleWishlist\Test\Unit\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EditwishlistTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $editWishListController;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $wishListEditor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $session;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $wishList;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var \Magento\Framework\Controller\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectMock;

    /**
     * @var \Magento\Framework\Controller\Result\Json|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultJsonMock;

    /** @var  int */
    protected $wishListId = 1;

    /** @var  int */
    protected $customerId = 1;

    protected $isAjax = false;

    protected $url;

    public function setUp()
    {
        $this->context = $this->getMock(
            'Magento\Framework\App\Action\Context',
            ['getMessageManager', 'getRequest', 'getResponse', 'getObjectManager', 'getUrl', 'getResultFactory'],
            [],
            '',
            false
        );
        $this->wishListEditor = $this->getMock(
            'Magento\MultipleWishlist\Model\WishlistEditor',
            ['edit'],
            [],
            '',
            false
        );
        $this->session = $this->getMock(
            'Magento\Customer\Model\Session',
            ['getCustomerId'],
            [],
            '',
            false
        );
        $this->request = $this->getMock(
            'Magento\Framework\App\Request\Http',
            ['getParam', 'isAjax'],
            [],
            '',
            false
        );
        $this->response = $this->getMock(
            'Magento\Framework\App\Response\Http',
            ['representJson'],
            [],
            '',
            false
        );
        $this->messageManager = $this->getMock(
            'Magento\Framework\Message\Manager',
            ['addSuccess', 'addError', 'addException'],
            [],
            '',
            false
        );
        $this->wishList = $this->getMock(
            'Magento\Wishlist\Model\Wishlist',
            ['getId', 'getName'],
            [],
            '',
            false
        );
        $this->objectManager = $this->getMock(
            'Magento\Framework\App\ObjectManager',
            ['get', 'jsonEncode', 'escapeHtml'],
            [],
            '',
            false
        );
        $this->url = $this->getMock(
            'Magento\Framework\Url',
            ['getUrl'],
            [],
            '',
            false
        );

        $this->resultFactoryMock = $this->getMockBuilder('Magento\Framework\Controller\ResultFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultRedirectMock = $this->getMockBuilder('Magento\Framework\Controller\Result\Redirect')
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultJsonMock = $this->getMockBuilder('Magento\Framework\Controller\Result\Json')
            ->disableOriginalConstructor()
            ->getMock();

        $this->context->expects($this->once())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);
    }

    public function tearDown()
    {
        unset($this->url,
            $this->objectManager,
            $this->wishList,
            $this->messageManager,
            $this->response,
            $this->request,
            $this->session,
            $this->wishListEditor,
            $this->context,
            $this->editWishListController
        );
    }

    public function createController()
    {
        $this->editWishListController = new \Magento\MultipleWishlist\Controller\Index\Editwishlist(
            $this->context,
            $this->wishListEditor,
            $this->session
        );
    }

    public function configureCustomerSession()
    {
        $this->session
            ->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue($this->customerId));
    }

    public function configureWishList($getIdExpects, $getNameExpects)
    {
        $this->wishList
            ->expects($this->exactly($getIdExpects))
            ->method('getId')
            ->will($this->returnValue($this->wishListId));
        $this->wishList
            ->expects($this->exactly($getNameExpects))
            ->method('getName')
            ->will($this->returnValue('wishlistTestName'));
    }

    public function configureObjectManager($getExpects, $jsonEncodeExpects, $escapeHtmlExpects)
    {
        $this->objectManager
            ->expects($this->exactly($getExpects))
            ->method('get')
            ->will($this->returnSelf());
        $this->objectManager
            ->expects($this->exactly($jsonEncodeExpects))
            ->method('jsonEncode')
            ->will($this->returnValue(null));
        $this->objectManager
            ->expects($this->exactly($escapeHtmlExpects))
            ->method('escapeHtml')
            ->with('wishlistTestName')
            ->will($this->returnValue('wishlistTestName'));
    }

    public function configureUrl($getUrlExpects)
    {
        $this->url
            ->expects($this->exactly($getUrlExpects))
            ->method('getUrl')
            ->will($this->returnValue(null));
    }

    public function configureContext()
    {
        $this->context
            ->expects($this->once())
            ->method('getMessageManager')
            ->will($this->returnValue($this->messageManager));
        $this->context
            ->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $this->context
            ->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $this->context
            ->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($this->response));
        $this->context
            ->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($this->url));
    }

    public function configureResponse($representJsonExpects)
    {
        $this->response
            ->expects($this->exactly($representJsonExpects))
            ->method('representJson')
            ->will($this->returnValue(null));
    }

    public function configureRequest()
    {
        $this->request
            ->expects($this->exactly(3))
            ->method('getParam')
            ->will($this->returnValue(null));
        $this->request
            ->expects($this->once())
            ->method('isAjax')
            ->will($this->returnValue($this->isAjax));
    }

    public function configure(
        $getIdExpects,
        $getNameExpects,
        $getExpects,
        $escapeHtmlExpects,
        $jsonEncodeExpects,
        $getUrlExpects,
        $representJsonExpects
    ) {
        $this->configureWishList($getIdExpects, $getNameExpects);
        $this->configureObjectManager($getExpects, $jsonEncodeExpects, $escapeHtmlExpects);
        $this->configureUrl($getUrlExpects);
        $this->configureResponse($representJsonExpects);
        $this->configureRequest();
        $this->configureContext();
        $this->configureCustomerSession();
        $this->createController();
    }

    public function testExecuteWishlistFrameworkException()
    {
        $exeption = new \Magento\Framework\Exception\LocalizedException(__('Sign in to edit wish lists.'));

        $this->messageManager
            ->expects($this->never())
            ->method('addSuccess')
            ->with('Wish list "wishlistTestName" was saved.')
            ->will($this->returnValue(null));
        $this->wishListEditor
            ->expects($this->once())
            ->method('edit')
            ->will($this->throwException($exeption));
        $this->messageManager
            ->expects($this->at(0))
            ->method('addError')
            ->with('Sign in to edit wish lists.')
            ->will($this->returnValue(null));
        $this->messageManager
            ->expects($this->at(1))
            ->method('addError')
            ->with('Could not create wishlist')
            ->will($this->returnValue(null));

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*', [])
            ->willReturnSelf();

        $this->resultFactoryMock->expects($this->any())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT, [])
            ->willReturn($this->resultRedirectMock);

        $this->configure(0, 0, 0, 0, 0, 0, 0);
        $this->assertInstanceOf(
            'Magento\Framework\Controller\Result\Redirect',
            $this->editWishListController->execute()
        );
    }

    public function testExecuteWishlistExceptionAndAjax()
    {
        $this->isAjax = true;
        $exeption = new \Exception(__('Sign in to edit wish lists.'));

        $this->messageManager
            ->expects($this->never())
            ->method('addSuccess')
            ->with('Wish list "wishlistTestName" was saved.')
            ->will($this->returnValue(null));
        $this->wishListEditor
            ->expects($this->once())
            ->method('edit')
            ->will($this->throwException($exeption));
        $this->messageManager
            ->expects($this->once())
            ->method('addError')
            ->with('Could not create wishlist')
            ->will($this->returnValue(null));
        $this->messageManager
            ->expects($this->once())
            ->method('addException')
            ->with($exeption, __('We can\'t create the wish list right now.'))
            ->will($this->returnValue(null));
        $this->objectManager
            ->expects($this->never())
            ->method('escapeHtml')
            ->with('wishlistTestName')
            ->will($this->returnValue('wishlistTestName'));
        $this->url
            ->expects($this->once())
            ->method('getUrl')
            ->with('*/*', null)
            ->will($this->returnValue('magento-test.com'));

        $this->configureWishList(0, 0);
        $this->configureRequest();
        $this->configureContext();
        $this->configureCustomerSession();
        $this->createController();

        $this->resultJsonMock->expects($this->once())
            ->method('setData')
            ->with(['redirect' => 'magento-test.com'], false, [])
            ->willReturnSelf();

        $this->resultFactoryMock->expects($this->any())
            ->method('create')
            ->with(ResultFactory::TYPE_JSON, [])
            ->willReturn($this->resultJsonMock);

        $this->assertInstanceOf(
            'Magento\Framework\Controller\Result\Json',
            $this->editWishListController->execute()
        );
    }

    public function testExecuteWithAjaxAndWishlist()
    {
        $this->isAjax = true;
        $this->configureWishList(3, 1);

        $this->messageManager
            ->expects($this->once())
            ->method('addSuccess')
            ->with('Wish list "wishlistTestName" was saved.')
            ->will($this->returnValue(null));
        $this->wishListEditor
            ->expects($this->once())
            ->method('edit')
            ->will($this->returnValue($this->wishList));
        $this->messageManager
            ->expects($this->never())
            ->method('addError')
            ->with('Could not create wishlist')
            ->will($this->returnValue(null));
        $this->messageManager
            ->expects($this->never())
            ->method('addException')
            ->will($this->returnValue(null));
        $this->objectManager
            ->expects($this->at(0))
            ->method('get')
            ->with('Magento\Framework\Escaper')
            ->will($this->returnSelf());
        $this->objectManager
            ->expects($this->at(1))
            ->method('escapeHtml')
            ->with('wishlistTestName')
            ->will($this->returnValue('wishlistTestName'));
        $this->url
            ->expects($this->never())
            ->method('getUrl')
            ->will($this->returnValue('magento-test.com'));

        $this->configureRequest();
        $this->configureContext();
        $this->configureCustomerSession();
        $this->createController();

        $this->resultJsonMock->expects($this->once())
            ->method('setData')
            ->with(['wishlist_id' => $this->wishListId], false, [])
            ->willReturnSelf();

        $this->resultFactoryMock->expects($this->any())
            ->method('create')
            ->with(ResultFactory::TYPE_JSON, [])
            ->willReturn($this->resultJsonMock);

        $this->assertInstanceOf(
            'Magento\Framework\Controller\Result\Json',
            $this->editWishListController->execute()
        );
    }

    public function testExecuteWithoutAjax()
    {
        $this->messageManager
            ->expects($this->once())
            ->method('addSuccess')
            ->with('Wish list "wishlistTestName" was saved.')
            ->will($this->returnValue(null));

        $this->messageManager
            ->expects($this->never())
            ->method('addError')
            ->will($this->returnValue(null));

        $this->wishListEditor
            ->expects($this->once())
            ->method('edit')
            ->will($this->returnValue($this->wishList));

        $this->configure(3, 1, 1, 1, 0, 0, 0);

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('wishlist/index/index', ['wishlist_id' => $this->wishListId])
            ->willReturnSelf();

        $this->resultFactoryMock->expects($this->any())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT, [])
            ->willReturn($this->resultRedirectMock);

        $this->assertInstanceOf(
            'Magento\Framework\Controller\Result\Redirect',
            $this->editWishListController->execute()
        );
    }
}
