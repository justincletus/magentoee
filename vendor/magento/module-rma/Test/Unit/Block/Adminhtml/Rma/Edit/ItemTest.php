<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Test\Unit\Block\Adminhtml\Rma\Edit;

/**
 * Class GridTest
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $formFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreRegistryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemFormFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemFactoryMock;

    /**
     * @var \Magento\Rma\Block\Adminhtml\Rma\Edit\Item
     */
    protected $item;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $escaperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilderMock;

    /**
     * Test setUp
     */
    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->formFactoryMock = $this->getMock(
            'Magento\Framework\Data\FormFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->coreRegistryMock = $this->getMock(
            'Magento\Framework\Registry',
            [],
            [],
            '',
            false
        );
        $this->contextMock = $this->getMock(
            'Magento\Backend\Block\Template\Context',
            ['getLayout', 'getEscaper', 'getUrlBuilder'],
            [],
            '',
            false
        );
        $this->escaperMock = $this->getMock(
            'Magento\Framework\Escaper',
            [],
            [],
            '',
            false
        );
        $this->layoutMock = $this->getMock(
            'Magento\Framework\View\Layout',
            ['createBlock'],
            [],
            '',
            false
        );
        $this->urlBuilderMock = $this->getMock(
            'Magento\Framework\Url',
            [],
            [],
            '',
            false
        );
        $this->contextMock->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($this->layoutMock));
        $this->contextMock->expects($this->any())
            ->method('getEscaper')
            ->will($this->returnValue($this->escaperMock));
        $this->contextMock->expects($this->any())
            ->method('getUrlBuilder')
            ->will($this->returnValue($this->urlBuilderMock));
        $this->rmaDataMock = $this->getMock(
            'Magento\Rma\Helper\Data',
            [],
            [],
            '',
            false
        );
        $this->itemFormFactoryMock = $this->getMock(
            'Magento\Rma\Model\Item\FormFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->itemFactoryMock = $this->getMock(
            'Magento\Sales\Model\Order\ItemFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->item = $objectManager->getObject(
            'Magento\Rma\Block\Adminhtml\Rma\Edit\Item',
            [
                'formFactory' => $this->formFactoryMock,
                'registry' => $this->coreRegistryMock,
                'context' => $this->contextMock,
                'rmaData' => $this->rmaDataMock,
                'itemFormFactory' => $this->itemFormFactoryMock,
                'itemFactory' => $this->itemFactoryMock,
            ]
        );
    }

    public function testInitForm()
    {
        $htmlPrefixId = 1;

        $item = $this->getMock(
            'Magento\Rma\Model\Item',
            [],
            [],
            '',
            false
        );

        $customerForm = $this->getMock(
            'Magento\Rma\Model\Item\Form',
            ['setEntity', 'setFormCode', 'initDefaultValues', 'getUserAttributes'],
            [],
            '',
            false
        );
        $customerForm->expects($this->any())
            ->method('setEntity')
            ->will($this->returnSelf());
        $customerForm->expects($this->any())
            ->method('setFormCode')
            ->will($this->returnSelf());
        $customerForm->expects($this->any())
            ->method('initDefaultValues')
            ->will($this->returnSelf());
        $customerForm->expects($this->any())
            ->method('getUserAttributes')
            ->will($this->returnValue([]));

        $this->itemFormFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($customerForm));

        $this->coreRegistryMock->expects($this->any())
            ->method('registry')
            ->with('current_rma_item')
            ->will($this->returnValue($item));

        $fieldsetMock = $this->getMock(
            'Magento\Framework\Data\Form\Element\Fieldset',
            [],
            [],
            '',
            false
        );

        $formMock = $this->getMock(
            'Magento\Framework\Data\Form',
            ['setHtmlIdPrefix', 'addFieldset', 'setValues', 'setParent', 'setBaseUrl'],
            [],
            '',
            false
        );
        $formMock->expects($this->once())
            ->method('setHtmlIdPrefix')
            ->with($htmlPrefixId . '_rma')
            ->will($this->returnValue($htmlPrefixId));
        $formMock->expects($this->any())
            ->method('addFieldset')
            ->will($this->returnValue($fieldsetMock));
        $this->formFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($formMock));

        $blockMock = $this->getMock(
            'Magento\Backend\Block\Widget\Button',
            [],
            [],
            '',
            false
        );
        $blockMock->expects($this->any())
            ->method('setData')
            ->will($this->returnSelf());

        $this->layoutMock->expects($this->any())
            ->method('createBlock')
            ->with('Magento\Backend\Block\Widget\Button')
            ->will($this->returnValue($blockMock));
        $this->item->setHtmlPrefixId($htmlPrefixId);
        $result = $this->item->initForm();
        $this->assertInstanceOf('Magento\Rma\Block\Adminhtml\Rma\Edit\Item', $result);
    }
}
