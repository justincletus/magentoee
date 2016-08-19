<?php
/***
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Block\Adminhtml\Cms\Page\Preview;

use Magento\Framework\App\Filesystem\DirectoryList;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Backend\Block\Template\Context
     */
    protected $context;

    /**
     * @var \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Preview\Form
     */
    protected $block;

    /**
     * @var \Magento\Framework\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileSystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $directory;

    /**
     * @var \Magento\Framework\Data\FormFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formFactory;

    /**
     * @var \Magento\Framework\Data\Form|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $form;

    /**
     * @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Data\Form\Element|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $element;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->urlBuilder = $this->getMock('Magento\Framework\UrlInterface');
        $this->formFactory = $this->getMock('Magento\Framework\Data\FormFactory', [], [], '', false);
        $this->directory = $this->getMock('Magento\Framework\Filesystem\Directory\ReadInterface');
        $this->fileSystem = $this->getMock('Magento\Framework\Filesystem', [], [], '', false);
        $this->objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->element = $this->getMock('Magento\Framework\Data\Form\Element', [], [], '', false);

        $contextArguments = $this->objectManagerHelper->getConstructArguments('Magento\Backend\Block\Template\Context');
        $contextArguments['urlBuilder'] = $this->urlBuilder;
        $contextArguments['filesystem'] = $this->fileSystem;
        $this->context = $this->objectManagerHelper->getObject(
            'Magento\Backend\Block\Template\Context',
            $contextArguments
        );

        /** @var \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Preview\Form $form */
        $this->block = $this->objectManagerHelper->getObject(
            'Magento\VersionsCms\Block\Adminhtml\Cms\Page\Preview\Form',
            [
                'formFactory' => $this->formFactory,
                'context' => $this->context
            ]
        );
    }

    /**
     * Test prepare form
     *
     * @param array $data
     * @param array $newData
     * @param int $fieldsCount
     * @param int $setValuesCount
     * @dataProvider testPrepareFormDataProvider
     */
    public function testPrepareForm(array $data, array $newData, $fieldsCount, $setValuesCount)
    {
        $action = 'http://localhost/' . \Magento\VersionsCms\Model\Page\Revision::PREVIEW_URI . '?app=cms_preview';

        $this->urlBuilder->expects($this->once())->method('getDirectUrl')
            ->with(\Magento\VersionsCms\Model\Page\Revision::PREVIEW_URI . '?app=cms_preview')
            ->willReturn($action);

        $this->form = $this->getMock('Magento\Framework\Data\Form', [], [], '', false);
        $this->form->expects($this->exactly($fieldsCount))->method('addField')->willReturnMap(
            [
                ['field1', 'hidden', ['name' => 'field1'], false, $this->element],
                ['fieldsetfield2', 'hidden', ['name' => 'fieldset[field2]'], false, $this->element]
            ]
        );

        $this->form->expects($this->exactly($setValuesCount))->method('setValues')->with($newData);

        $this->formFactory->expects($this->once())->method('create')
            ->with(
                [
                    'data' => [
                        'id' => 'preview_form',
                        'action' => $action,
                        'method' => 'post',
                    ],
                ]
            )
            ->willReturn($this->form);

        $this->fileSystem->expects($this->once())->method('getDirectoryRead')
            ->with(DirectoryList::ROOT)
            ->willReturn($this->directory);

        $this->block->setFormData($data);
        $this->block->toHtml();
    }

    /**
     * @return array
     */
    public function testPrepareFormDataProvider()
    {
        return [
            [
                ['field1' => 'value1', 'fieldset' => ['field2' => 'value2']],
                ['field1' => 'value1', 'fieldsetfield2' => 'value2'],
                2,
                1
            ],
            [
                [],
                [],
                0,
                0
            ]
        ];
    }
}
