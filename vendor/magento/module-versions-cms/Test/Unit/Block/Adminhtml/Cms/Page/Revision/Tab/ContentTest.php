<?php
/***
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VersionsCms\Test\Unit\Block\Adminhtml\Cms\Page\Revision\Tab;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ContentTest extends \PHPUnit_Framework_TestCase
{
    const FIELD_SET_CLASS = 'Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $renderer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $block;

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
    protected $contentField;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userIdElement;

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
    protected $user;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendAuthSession;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cmsData;

    /** @var  \Magento\Framework\View\Element\Template\File\Resolver|\PHPUnit_Framework_MockObject_MockObject */
    protected $resolver;

    /** @var  \Magento\Framework\View\Element\Template\File\Resolver|\PHPUnit_Framework_MockObject_MockObject */
    protected $validator;

    public function setUp()
    {
        $this->renderer = $this->getMock(
            'Magento\Framework\Data\Form\Element\Renderer\RendererInterface',
            [],
            [],
            '',
            false
        );
        $this->block = $this->getMock(self::FIELD_SET_CLASS, [], [], '', false);
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
        $this->contentField = $this->getMock('Magento\Framework\Data\Form\Element\AbstractElement', [], [], '', false);
        $this->userIdElement = $this->getMock(
            'Magento\Framework\Data\Form\Element\AbstractElement',
            ['setValue'],
            [],
            '',
            false
        );
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
        $this->user = $this->getMock('Magento\User\Model\User', [], [], '', false);
        $this->backendAuthSession = $this->getMock('Magento\Backend\Model\Auth\Session', ['getUser'], [], '', false);
        $this->cmsData = $this->getMock('Magento\VersionsCms\Helper\Data', [], [], '', false);
        $this->resolver = $this->getMock('\Magento\Framework\View\Element\Template\File\Resolver', [], [], '', false);
        $this->validator = $this->getMock('\Magento\Framework\View\Element\Template\File\Validator', [], [], '', false);
    }

    public function testPrepareFormNewPageShouldNotAddDataFields()
    {
        $this->prepareForm(null);
        $this->context->expects($this->any())
            ->method('getResolver')
            ->will($this->returnValue($this->resolver));

        $this->context->expects($this->any())
            ->method('getValidator')
            ->will($this->returnValue($this->validator));

        $this->validator->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));

        /** @var \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Revision\Edit\Tab\Content $model */
        $model = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))
            ->getObject(
                'Magento\VersionsCms\Block\Adminhtml\Cms\Page\Revision\Edit\Tab\Content',
                [
                    'formFactory' => $this->formFactory,
                    'context' => $this->context,
                    'registry' => $this->registry,
                    'backendAuthSession' => $this->backendAuthSession,
                    'cmsData' => $this->cmsData,
                ]
            );
        $model->toHtml();
    }

    public function testPrepareFormEditPageShouldAddDataFields()
    {
        $this->prepareForm(1);

        $this->context->expects($this->any())
            ->method('getResolver')
            ->will($this->returnValue($this->resolver));

        $this->context->expects($this->any())
            ->method('getValidator')
            ->will($this->returnValue($this->validator));

        $this->validator->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->fieldSet->expects($this->at(2))->method('addField')->with('page_id', 'hidden', ['name' => 'page_id']);
        $this->fieldSet->expects($this->at(3))
            ->method('addField')
            ->with('version_id', 'hidden', ['name' => 'version_id']);
        $this->fieldSet->expects($this->at(4))
            ->method('addField')
            ->with('revision_id', 'hidden', ['name' => 'revision_id']);
        $this->fieldSet->expects($this->at(5))->method('addField')->with('label', 'hidden', ['name' => 'label']);
        $this->fieldSet->expects($this->at(6))->method('addField')->with('user_id', 'hidden', ['name' => 'user_id']);

        /** @var \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Revision\Edit\Tab\Content $model */
        $model = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))
            ->getObject(
                'Magento\VersionsCms\Block\Adminhtml\Cms\Page\Revision\Edit\Tab\Content',
                [
                    'formFactory' => $this->formFactory,
                    'context' => $this->context,
                    'registry' => $this->registry,
                    'backendAuthSession' => $this->backendAuthSession,
                    'cmsData' => $this->cmsData
                ]
            );
        $model->toHtml();
    }

    /**
     * @param int|null $pageId
     */
    protected function prepareForm($pageId)
    {
        $isElementDisabled = true;
        $wysiwygConfig = null;
        $absolutePath = '/absolute/path';
        $templateFileName = $absolutePath . '/to/file';
        $modelData = ['data'];
        $userId = 1;

        $this->block->expects($this->once())
            ->method('setTemplate')
            ->with('Magento_Cms::page/edit/form/renderer/content.phtml')
            ->willReturn($this->renderer);

        $this->layout->expects($this->once())
            ->method('createBlock')
            ->with(self::FIELD_SET_CLASS)
            ->willReturn($this->block);

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

        $this->userIdElement->expects($this->once())->method('setValue')->with($userId);

        $this->form->expects($this->once())->method('addFieldset')->willReturn($this->fieldSet);
        $this->form->expects($this->atLeastOnce())->method('getElement')->willReturnMap(
            [
                ['user_id', $this->userIdElement],
                ['content_fieldset', $this->fieldSet]
            ]
        );
        $this->form->expects($this->atLeastOnce())->method('setValues')->with($modelData);

        $this->formFactory->expects($this->once())->method('create')->willReturn($this->form);

        $this->cmsModel->expects($this->once())->method('getPageId')->willReturn($pageId);
        $this->cmsModel->expects($this->atLeastOnce())->method('getData')->willReturn($modelData);

        $this->registry->expects($this->atLeastOnce())->method('registry')->with('cms_page')->willReturn(
            $this->cmsModel
        );

        $this->user->expects($this->once())->method('getId')->willReturn($userId);
        $this->backendAuthSession->expects($this->once())->method('getUser')->willReturn($this->user);

        $this->cmsData->expects($this->once())
            ->method('addAttributeToFormElements')
            ->with('data-role', 'cms-revision-form-changed', $this->form);

        $this->fieldSet->expects($this->at(1))
            ->method('addField')
            ->with(
                'content',
                'editor',
                [
                    'name' => 'content',
                    'style' => 'height:36em;',
                    'required' => true,
                    'disabled' => $isElementDisabled,
                    'config' => $wysiwygConfig
                ]
            )
            ->willReturn($this->contentField);
    }
}
