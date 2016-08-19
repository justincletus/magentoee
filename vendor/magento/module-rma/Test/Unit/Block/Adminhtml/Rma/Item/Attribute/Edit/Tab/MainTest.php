<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Rma\Test\Unit\Block\Adminhtml\Rma\Item\Attribute\Edit\Tab;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class MainTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Rma\Block\Adminhtml\Rma\Item\Attribute\Edit\Tab\Main */
    protected $main;

    /** @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject */
    protected $requestInterface;

    /** @var \Magento\Framework\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $layoutInterface;

    /** @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $managerInterface;

    /** @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlInterface;

    /** @var \Magento\Framework\App\CacheInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $cacheInterface;

    /** @var \Magento\Framework\View\DesignInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $designInterface;

    /** @var \Magento\Framework\Session\Generic|\PHPUnit_Framework_MockObject_MockObject */
    protected $session;

    /** @var \Magento\Framework\Session\SidResolverInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $sidResolverInterface;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $scopeConfig;

    /** @var \Magento\Framework\View\Asset\Repository|\PHPUnit_Framework_MockObject_MockObject */
    protected $assetRepo;

    /** @var \Magento\Framework\View\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $configInterface;

    /** @var \Magento\Framework\App\Cache\StateInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $cacheState;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $logger;

    /** @var \Magento\Framework\Escaper|\PHPUnit_Framework_MockObject_MockObject */
    protected $escaper;

    /** @var \Magento\Framework\Filter\FilterManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $filterManager;

    /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $timezoneInterface;

    /** @var \Magento\Framework\Translate\Inline\StateInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $translateState;

    /** @var \Magento\Framework\Filesystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $appFilesystem;

    /** @var \Magento\Framework\View\FileSystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $viewFilesystem;

    /** @var \Magento\Framework\View\TemplateEnginePool|\PHPUnit_Framework_MockObject_MockObject */
    protected $templateEnginePool;

    /** @var \Magento\Framework\App\State|\PHPUnit_Framework_MockObject_MockObject */
    protected $appState;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManagerInterface;

    /** @var \Magento\Framework\AuthorizationInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $authorizationInterface;

    /** @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $backendSession;

    /** @var \Magento\Framework\Math\Random|\PHPUnit_Framework_MockObject_MockObject */
    protected $random;

    /** @var \Magento\Framework\Data\Form\FormKey|\PHPUnit_Framework_MockObject_MockObject */
    protected $formKey;

    /** @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $pageConfig;

    /** @var \Magento\Framework\Code\NameBuilder|\PHPUnit_Framework_MockObject_MockObject */
    protected $nameBuilder;

    /** @var \Magento\Backend\Block\Template\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $context;

    /** @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject */
    protected $registry;

    /** @var \Magento\Framework\Data\FormFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $formFactory;

    /** @var \Magento\Eav\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $eavHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $yesnoFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $inputtypeFactory;

    /** @var \Magento\CustomAttributeManagement\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $customAttributeManagementHelper;

    /** @var \Magento\Rma\Helper\Eav|\PHPUnit_Framework_MockObject_MockObject */
    protected $rmaEavHelper;

    /** @var  \Magento\Framework\View\Element\Template\File\Resolver|\PHPUnit_Framework_MockObject_MockObject */
    protected $resolver;

    /** @var  \Magento\Framework\View\Element\Template\File\Validator|\PHPUnit_Framework_MockObject_MockObject */
    protected $validator;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp()
    {
        $this->requestInterface = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()->getMock();
        $this->layoutInterface = $this->getMock('Magento\Framework\View\LayoutInterface');
        $this->managerInterface = $this->getMock('Magento\Framework\Event\ManagerInterface');
        $this->urlInterface = $this->getMock('Magento\Framework\UrlInterface');
        $this->cacheInterface = $this->getMock('Magento\Framework\App\CacheInterface');
        $this->designInterface = $this->getMock('Magento\Framework\View\DesignInterface');
        $this->session = $this->getMock('Magento\Framework\Session\Generic', [], [], '', false);
        $this->sidResolverInterface = $this->getMock('Magento\Framework\Session\SidResolverInterface');
        $this->scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->assetRepo = $this->getMock('Magento\Framework\View\Asset\Repository', [], [], '', false);
        $this->configInterface = $this->getMock('Magento\Framework\View\ConfigInterface');
        $this->cacheState = $this->getMock('Magento\Framework\App\Cache\StateInterface');
        $this->logger = $this->getMock('Psr\Log\LoggerInterface');
        $this->escaper = $this->getMock('Magento\Framework\Escaper');
        $this->filterManager = $this->getMock('Magento\Framework\Filter\FilterManager', [], [], '', false);
        $this->timezoneInterface = $this->getMock('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $this->translateState = $this->getMock('Magento\Framework\Translate\Inline\StateInterface');
        $this->appFilesystem = $this->getMock('Magento\Framework\Filesystem', [], [], '', false);
        $this->viewFilesystem = $this->getMock('Magento\Framework\View\FileSystem', [], [], '', false);
        $this->templateEnginePool = $this->getMock('Magento\Framework\View\TemplateEnginePool', [], [], '', false);
        $this->appState = $this->getMock('Magento\Framework\App\State', [], [], '', false);
        $this->storeManagerInterface = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->authorizationInterface = $this->getMock('Magento\Framework\AuthorizationInterface');
        $this->backendSession = $this->getMock('Magento\Backend\Model\Session', [], [], '', false);
        $this->random = $this->getMock('Magento\Framework\Math\Random');
        $this->formKey = $this->getMock('Magento\Framework\Data\Form\FormKey', [], [], '', false);
        $this->nameBuilder = $this->getMock('Magento\Framework\Code\NameBuilder', [], [], '', false);
        $this->pageConfig = $this->getMock('Magento\Framework\View\Page\Config', [], [], '', false);
        $this->resolver = $this->getMock('\Magento\Framework\View\Element\Template\File\Resolver', [], [], '', false);
        $this->validator = $this->getMock('\Magento\Framework\View\Element\Template\File\Validator', [], [], '', false);

        $this->context = $this->getMock(
            'Magento\Backend\Block\Template\Context',
            null,
            [
                'request' => $this->requestInterface,
                'layout' => $this->layoutInterface,
                'eventManager' => $this->managerInterface,
                'urlBuilder' => $this->urlInterface,
                'cache' => $this->cacheInterface,
                'design' => $this->designInterface,
                'session' => $this->session,
                'sidResolver' => $this->sidResolverInterface,
                'storeConfig' => $this->scopeConfig,
                'assetRepo' => $this->assetRepo,
                'viewConfig' => $this->configInterface,
                'cacheState' => $this->cacheState,
                'logger' => $this->logger,
                'escaper' => $this->escaper,
                'filterManager' => $this->filterManager,
                'localeDate' => $this->timezoneInterface,
                'inlineTranslation' => $this->translateState,
                'filesystem' => $this->appFilesystem,
                'viewFileSystem' => $this->viewFilesystem,
                'enginePool' => $this->templateEnginePool,
                'appState' => $this->appState,
                'storeManager' => $this->storeManagerInterface,
                'pageConfig' => $this->pageConfig,
                'resolver' => $this->resolver,
                'validator' => $this->validator,
                'authorization' => $this->authorizationInterface,
                'backendSession' => $this->backendSession,
                'mathRandom' => $this->random,
                'formKey' => $this->formKey,
                'nameBuilder' => $this->nameBuilder
            ]
        );

        $this->registry = $this->getMock('Magento\Framework\Registry');
        $this->formFactory = $this->getMock('Magento\Framework\Data\FormFactory', [], [], '', false);
        $this->eavHelper = $this->getMock('Magento\Eav\Helper\Data', [], [], '', false);
        $this->yesnoFactory = $this->getMock(
            'Magento\Config\Model\Config\Source\YesnoFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->inputtypeFactory = $this->getMock(
            'Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->scopeConfig = $this->getMock('Magento\Eav\Model\Entity\Attribute\Config', [], [], '', false);
        $this->customAttributeManagementHelper = $this->getMock(
            'Magento\CustomAttributeManagement\Helper\Data',
            [],
            [],
            '',
            false
        );
        $this->rmaEavHelper = $this->getMock('Magento\Rma\Helper\Eav', [], [], '', false);

        $this->main = (new ObjectManagerHelper($this))->getObject(
            'Magento\Rma\Block\Adminhtml\Rma\Item\Attribute\Edit\Tab\Main',
            [
                'context' => $this->context,
                'registry' => $this->registry,
                'formFactory' => $this->formFactory,
                'eavData' => $this->eavHelper,
                'yesnoFactory' => $this->yesnoFactory,
                'inputTypeFactory' => $this->inputtypeFactory,
                'attributeConfig' => $this->scopeConfig,
                'attributeHelper' => $this->customAttributeManagementHelper,
                'rmaEav' => $this->rmaEavHelper
            ]
        );
    }

    public function testUsedInFormsAndIsVisibleFieldsDependency()
    {
        $fieldset = $this->getMock('Magento\Framework\Data\Form\Element\Fieldset', [], [], '', false);
        $fieldset->expects($this->any())->method('addField')->will($this->returnSelf());
        $form = $this->getMock('Magento\Framework\Data\Form', ['addFieldset', 'getElement'], [], '', false);
        $form->expects($this->any())->method('addFieldset')->will($this->returnValue($fieldset));
        $form->expects($this->any())->method('getElement')->will($this->returnValue($fieldset));
        $this->formFactory->expects($this->any())->method('create')->will($this->returnValue($form));

        $yesno = $this->getMock('Magento\Config\Model\Config\Source\Yesno', [], [], '', false);
        $this->yesnoFactory->expects($this->any())->method('create')->will($this->returnValue($yesno));

        $inputtype = $this->getMock('Magento\Config\Model\Config\Source\Yesno', [], [], '', false);
        $this->inputtypeFactory->expects($this->any())->method('create')
            ->will($this->returnValue($inputtype));

        $this->customAttributeManagementHelper->expects($this->any())->method('getAttributeElementScopes')
            ->will($this->returnValue([]));

        $this->customAttributeManagementHelper->expects($this->any())->method('getFrontendInputOptions')
            ->will($this->returnValue([]));

        $dependenceBlock = $this->getMock('Magento\Backend\Block\Widget\Form\Element\Dependence', [], [], '', false);
        $dependenceBlock->expects($this->any())->method('addFieldMap')->will($this->returnSelf());

        $this->layoutInterface->expects($this->once())->method('createBlock')
            ->with('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->will($this->returnValue($dependenceBlock));
        $this->layoutInterface->expects($this->any())->method('setChild')->with(null, null, 'form_after')
            ->will($this->returnSelf());

        $this->appFilesystem->expects($this->any())->method('getDirectoryRead')
            ->will($this->throwException(new \Exception('test')));

        $this->main->setAttributeObject(
            new \Magento\Framework\DataObject(['entity_type' => new \Magento\Framework\DataObject([])])
        );

        try {
            $this->main->toHtml();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals('test', $e->getMessage());
        }
    }
}
