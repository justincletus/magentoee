<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GoogleTagManager\Test\Unit\Block\Adminhtml\Banner\Edit\Tab;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class GaTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GoogleTagManager\Block\Adminhtml\Banner\Edit\Tab\Ga */
    protected $ga;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject */
    protected $registry;

    /** @var \Magento\Framework\Data\FormFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $formFactory;

    /** @var \Magento\GoogleTagManager\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $googleTagManagerHelper;

    protected function setUp()
    {
        $this->registry = $this->getMock('Magento\Framework\Registry');
        $this->formFactory = $this->getMock('Magento\Framework\Data\FormFactory', ['create'], [], '', false);
        $this->googleTagManagerHelper = $this->getMock('Magento\GoogleTagManager\Helper\Data', [], [], '', false);
        $directory = $this->getMock('Magento\Framework\Filesystem\Directory\ReadInterface', [], [], '', false);
        $filesystem = $this->getMock('Magento\Framework\Filesystem', [], [], '', false);
        $filesystem->expects($this->any())->method('getDirectoryRead')->willReturn($directory);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->ga = $this->objectManagerHelper->getObject(
            'Magento\GoogleTagManager\Block\Adminhtml\Banner\Edit\Tab\Ga',
            [
                'registry' => $this->registry,
                'formFactory' => $this->formFactory,
                'helper' => $this->googleTagManagerHelper,
                'filesystem' => $filesystem
            ]
        );
    }

    /**
     * @covers \Magento\GoogleTagManager\Block\Adminhtml\Banner\Edit\Tab\Ga::_prepareForm
     */
    public function testToHtml()
    {
        $this->googleTagManagerHelper->expects($this->any())->method('isGoogleAnalyticsAvailable')->willReturn(true);
        $fieldset = $this->getMock('Magento\Framework\Data\Form\Element\Fieldset', [], [], '', false);
        $fieldset->expects($this->any())->method('addField')->withConsecutive(
            [
                'is_ga_enabled',
                'select',
                [
                    'label' => 'Send to Google',
                    'name' => 'is_ga_enabled',
                    'required' => false,
                    'options' => [
                        1 => 'Yes',
                        0 => 'No',
                    ],
                ]
            ],
            [
                'ga_creative',
                'text',
                [
                    'label' => 'Creative',
                    'name' => 'ga_creative',
                    'required' => false,
                ]
            ]
        )->willReturnSelf();

        $banner = $this->getMock('Magento\Banner\Model\Banner', [], [], '', false);
        $banner->expects($this->atLeastOnce())->method('getId')->willReturn(null);
        $banner->expects($this->atLeastOnce())->method('setData')->with('is_ga_enabled', 1)->willReturnSelf();
        $banner->expects($this->atLeastOnce())->method('getData')->willReturn(['name' => 'test']);

        $form = $this->getMock(
            'Magento\Framework\Data\Form',
            [
                'setHtmlIdPrefix',
                'addFieldset',
                'setValues'
            ],
            [],
            '',
            false
        );
        $form->expects($this->once())->method('setHtmlIdPrefix')->with('banner_googleanalytics_settings_')
            ->willReturnSelf();
        $form->expects($this->once())->method('addFieldset')->with(
            'ga_fieldset',
            ['legend' => 'Google Analytics Enhanced Ecommerce Settings']
        )->willReturn($fieldset);
        $form->expects($this->once())->method('setValues')->with(['name' => 'test']);

        $this->registry->expects($this->any())->method('registry')->with('current_banner')->willReturn($banner);

        $this->formFactory->expects($this->any())->method('create')->willReturn($form);
        $this->ga->toHtml();
    }

    /**
     * @covers \Magento\GoogleTagManager\Block\Adminhtml\Banner\Edit\Tab\Ga::_prepareForm
     */
    public function testToHtmlGaDisabled()
    {
        $this->googleTagManagerHelper->expects($this->any())->method('isGoogleAnalyticsAvailable')->willReturn(false);
        $this->formFactory->expects($this->never())->method('create');
    }
}
