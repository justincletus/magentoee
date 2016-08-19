<?php
/***
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VersionsCms\Test\Unit\Block\Adminhtml\Cms\Page\Revision\Tab;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class DesignTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $authorization;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $appState;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $directory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileSystem;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateEngine;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateEnginePull;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $abstractElement;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldSet;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $form;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cmsModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $formFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendAuthSession;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cmsData;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageLayoutsConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageLayoutBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDate;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $labelFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $label;

    /** @var  \Magento\Framework\View\Element\Template\File\Resolver|\PHPUnit_Framework_MockObject_MockObject */
    protected $resolver;

    /** @var  \Magento\Framework\View\Element\Template\File\Resolver|\PHPUnit_Framework_MockObject_MockObject */
    protected $validator;

    public function setUp()
    {
        $this->layout = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);
        $this->eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface', [], [], '', false);
        $this->scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface', [], [], '', false);
        $this->authorization = $this->getMock('Magento\Framework\AuthorizationInterface', [], [], '', false);
        $this->urlBuilder = $this->getMock('Magento\Framework\UrlInterface', [], [], '', false);
        $this->appState = $this->getMock('Magento\Framework\App\State', [], [], '', false);
        $this->directory = $this->getMock('Magento\Framework\Filesystem\Directory\ReadInterface', [], [], '', false);
        $this->templateEngine = $this->getMock('Magento\Framework\View\TemplateEngineInterface');
        $this->templateEnginePull = $this->getMock('Magento\Framework\View\TemplateEnginePool', [], [], '', false);
        $this->context = $this->getMock('Magento\Backend\Block\Template\Context', [], [], '', false);

        $this->abstractElement = $this->getMock(
            'Magento\Framework\Data\Form\Element\AbstractElement',
            [],
            [],
            '',
            false
        );
        $this->fieldSet = $this->getMock('Magento\Framework\Data\Form\Element\Fieldset', [], [], '', false);
        $this->form = $this->getMock('Magento\Framework\Data\Form', [], [], '', false);
        $this->cmsModel = $this->getMock('Magento\Cms\Model\Page', ['getPageId', 'getData'], [], '', false);
        $this->formFactory = $this->getMock('Magento\Framework\Data\FormFactory', [], [], '', false);
        $this->registry = $this->getMock('Magento\Framework\Registry', [], [], '', false);
        $this->backendAuthSession = $this->getMock('Magento\Backend\Model\Auth\Session', ['getUser'], [], '', false);
        $this->cmsData = $this->getMock('Magento\VersionsCms\Helper\Data', [], [], '', false);
        $this->pageLayoutsConfig = $this->getMock('Magento\Framework\View\PageLayout\Config', [], [], '', false);
        $this->pageLayoutBuilder = $this->getMock(
            'Magento\Framework\View\Model\PageLayout\Config\BuilderInterface',
            [],
            [],
            '',
            false
        );
        $this->localeDate = $this->getMock('Magento\Framework\Stdlib\DateTime\TimezoneInterface', [], [], '', false);
        $this->labelFactory = $this->getMock('Magento\Framework\View\Design\Theme\LabelFactory', [], [], '', false);
        $this->label = $this->getMock('Magento\Framework\View\Design\Theme\Label', [], [], '', false);
        $this->resolver = $this->getMock('\Magento\Framework\View\Element\Template\File\Resolver', [], [], '', false);
        $this->validator = $this->getMock('\Magento\Framework\View\Element\Template\File\Validator', [], [], '', false);
    }

    public function testPrepareFormNewPage()
    {
        $this->context->expects($this->any())
            ->method('getResolver')
            ->will($this->returnValue($this->resolver));

        $this->context->expects($this->any())
            ->method('getValidator')
            ->will($this->returnValue($this->validator));

        $this->validator->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));

        $absolutePath = '/absolute/path';
        $templateFileName = $absolutePath . '/to/file';
        $modelData = ['data'];

        $this->resolver->expects($this->once())
            ->method('getTemplateFileName')
            ->with('Magento_Backend::widget/form.phtml', ['module' => 'Magento_VersionsCms'])
            ->willReturn($templateFileName);

        $this->fileSystem = $this->getMock('Magento\Framework\FileSystem', [], [], '', false);
        $this->fileSystem->expects($this->any())->method('getDirectoryRead')->willReturn($this->directory);

        $this->templateEnginePull->expects($this->once())->method('get')->willReturn($this->templateEngine);

        $this->context->expects($this->once())->method('getLayout')->willReturn($this->layout);
        $this->context->expects($this->once())->method('getEventManager')->willReturn($this->eventManager);
        $this->context->expects($this->once())->method('getScopeConfig')->willReturn($this->scopeConfig);
        $this->context->expects($this->once())->method('getAuthorization')->willReturn($this->authorization);
        $this->context->expects($this->once())->method('getUrlBuilder')->willReturn($this->urlBuilder);
        $this->context->expects($this->once())->method('getAppState')->willReturn($this->appState);
        $this->context->expects($this->once())->method('getFilesystem')->willReturn($this->fileSystem);
        $this->context->expects($this->once())->method('getEnginePool')->willReturn($this->templateEnginePull);
        $this->context->expects($this->atLeastOnce())->method('getLocaleDate')->willReturn($this->localeDate);

        $this->form->expects($this->atLeastOnce())->method('addFieldset')->willReturn($this->fieldSet);
        $this->form->expects($this->atLeastOnce())->method('setValues')->with($modelData);

        $this->formFactory->expects($this->once())->method('create')->willReturn($this->form);

        $this->cmsModel->expects($this->atLeastOnce())->method('getData')->willReturn($modelData);

        $this->registry->expects($this->atLeastOnce())->method('registry')->with('cms_page')->willReturn(
            $this->cmsModel
        );

        $this->cmsData->expects($this->once())
            ->method('addAttributeToFormElements')
            ->with('data-role', 'cms-revision-form-changed', $this->form);

        $this->pageLayoutBuilder->expects($this->atLeastOnce())
            ->method('getPageLayoutsConfig')
            ->willReturn($this->pageLayoutsConfig);

        $this->labelFactory->expects($this->once())->method('create')->willReturn($this->label);

        /** @var \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Revision\Edit\Tab\Design $model */
        $model = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))
            ->getObject(
                'Magento\VersionsCms\Block\Adminhtml\Cms\Page\Revision\Edit\Tab\Design',
                [
                    'formFactory' => $this->formFactory,
                    'context' => $this->context,
                    'registry' => $this->registry,
                    'backendAuthSession' => $this->backendAuthSession,
                    'cmsData' => $this->cmsData,
                    'pageLayoutBuilder' => $this->pageLayoutBuilder,
                    'labelFactory' => $this->labelFactory
                ]
            );
        $model->toHtml();
    }
}
