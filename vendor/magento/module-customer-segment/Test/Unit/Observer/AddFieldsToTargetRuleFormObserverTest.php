<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\CustomerSegment\Test\Unit\Observer;

class AddFieldsToTargetRuleFormObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerSegment\Observer\AddFieldsToTargetRuleFormObserver
     */
    private $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_segmentHelper;

    protected function setUp()
    {
        $this->_segmentHelper = $this->getMock(
            'Magento\CustomerSegment\Helper\Data',
            ['isEnabled', 'addSegmentFieldsToForm'],
            [],
            '',
            false
        );

        $this->_model = new \Magento\CustomerSegment\Observer\AddFieldsToTargetRuleFormObserver(
            $this->_segmentHelper
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_segmentHelper = null;
    }

    public function testAddFieldsToTargetRuleForm()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $formDependency = $this->getMock(
            'Magento\Backend\Block\Widget\Form\Element\Dependence',
            [],
            [],
            '',
            false
        );

        $layout = $this->getMock('Magento\Framework\View\Layout', ['createBlock'], [], '', false);
        $layout->expects(
            $this->once()
        )->method(
            'createBlock'
        )->with(
            'Magento\Backend\Block\Widget\Form\Element\Dependence'
        )->will(
            $this->returnValue($formDependency)
        );

        $factoryElement = $this->getMock('Magento\Framework\Data\Form\Element\Factory', [], [], '', false);
        $collectionFactory = $this->getMock(
            'Magento\Framework\Data\Form\Element\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $formKey = $this->getMock('Magento\Framework\Data\Form\FormKey', [], [], '', false);
        $form = new \Magento\Framework\Data\Form($factoryElement, $collectionFactory, $formKey);
        $model = new \Magento\Framework\DataObject();
        $block = new \Magento\Framework\DataObject(['layout' => $layout]);

        $this->_segmentHelper->expects(
            $this->once()
        )->method(
            'addSegmentFieldsToForm'
        )->with(
            $form,
            $model,
            $formDependency
        );

        $this->_model->execute(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\DataObject(
                        ['form' => $form, 'model' => $model, 'block' => $block]
                    )
                ]
            )
        );
    }

    public function testAddFieldsToTargetRuleFormDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $layout = $this->getMock('Magento\Framework\View\Layout', ['createBlock'], [], '', false);
        $layout->expects($this->never())->method('createBlock');

        $factoryElement = $this->getMock('Magento\Framework\Data\Form\Element\Factory', [], [], '', false);
        $collectionFactory = $this->getMock(
            'Magento\Framework\Data\Form\Element\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $formKey = $this->getMock('Magento\Framework\Data\Form\FormKey', [], [], '', false);
        $form = new \Magento\Framework\Data\Form($factoryElement, $collectionFactory, $formKey);
        $model = new \Magento\Framework\DataObject();
        $block = new \Magento\Framework\DataObject(['layout' => $layout]);

        $this->_segmentHelper->expects($this->never())->method('addSegmentFieldsToForm');

        $this->_model->execute(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\DataObject(
                        ['form' => $form, 'model' => $model, 'block' => $block]
                    )
                ]
            )
        );
    }
}
