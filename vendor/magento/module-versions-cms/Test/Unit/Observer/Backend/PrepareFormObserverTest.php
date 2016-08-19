<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Test\Unit\Observer\Backend;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Cms\Model\Page;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Data\Form\Element\Select;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\VersionsCms\Model\Config;
use Magento\VersionsCms\Model\Page\RevisionFactory;
use Magento\VersionsCms\Observer\Backend\PrepareFormObserver;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PrepareFormObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Page|MockObject
     */
    protected $pageMock;

    /**
     * @var Config|MockObject
     */
    protected $configMock;

    /**
     * @var Yesno|MockObject
     */
    protected $sourceYesnoMock;

    /**
     * @var Registry|MockObject
     */
    protected $coreRegistryMock;

    /**
     * @var RevisionFactory|MockObject
     */
    protected $revisionFactoryMock;

    /**
     * @var UrlInterface|MockObject
     */
    protected $backendUrlMock;

    /**
     * @var AuthorizationInterface|MockObject
     */
    protected $authorizationMock;

    /**
     * @var Session|MockObject
     */
    protected $backendAuthSessionMock;

    /**
     * @var Observer|MockObject
     */
    protected $eventObserverMock;

    /**
     * @var Fieldset|MockObject
     */
    protected $fieldsetMock;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var PrepareFormObserver
     */
    protected $observer;

    /**
     * @var int
     */
    protected $pageId = 1;

    /**
     * @var int
     */
    protected $revisionId = 2;

    /**
     * @var string
     */
    protected $url = 'localhost/some_url';

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->pageMock = $this->getMockBuilder('Magento\Cms\Model\Page')
            ->disableOriginalConstructor()
            ->setMethods([
                'getUnderVersionControl', 'getId', 'setPublishedRevisionStatus',
                'getPublishedRevisionId', 'setPublishedRevisionLink'
            ])
            ->getMock();
        $this->configMock = $this->getMock('Magento\VersionsCms\Model\Config', [], [], '', false);
        $this->sourceYesnoMock = $this->getMock('Magento\Config\Model\Config\Source\Yesno', [], [], '', false);
        $this->coreRegistryMock = $this->getMock('Magento\Framework\Registry', [], [], '', false);
        $this->revisionFactoryMock = $this->getMockBuilder('Magento\VersionsCms\Model\Page\RevisionFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->backendUrlMock = $this->getMock('Magento\Backend\Model\UrlInterface');
        $this->authorizationMock = $this->getMock('Magento\Framework\AuthorizationInterface');
        $this->backendAuthSessionMock = $this->getMockBuilder('Magento\Backend\Model\Auth\Session')
            ->disableOriginalConstructor()
            ->setMethods(['getUser'])
            ->getMock();
        $this->eventObserverMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $this->fieldsetMock = $this->getMock('Magento\Framework\Data\Form\Element\Fieldset', [], [], '', false);

        $this->coreRegistryMock->expects($this->once())
            ->method('registry')
            ->with('cms_page')
            ->willReturn($this->pageMock);

        $this->observer = $this->objectManagerHelper->getObject(
            'Magento\VersionsCms\Observer\Backend\PrepareFormObserver',
            [
                'config' => $this->configMock,
                'sourceYesno' => $this->sourceYesnoMock,
                'coreRegistry' => $this->coreRegistryMock,
                'revisionFactory' => $this->revisionFactoryMock,
                'backendUrl' => $this->backendUrlMock,
                'authorization' => $this->authorizationMock,
                'backendAuthSession' => $this->backendAuthSessionMock
            ]
        );
    }

    /**
     * @return void
     */
    public function testCurrentUserCannotPublishRevision()
    {
        /** @var Select|MockObject $isActiveMock */
        $isActiveMock = $this->getMock('Magento\Framework\Data\Form\Element\Select', ['setDisabled'], [], '', false);
        $isActiveMock->expects($this->once())
            ->method('setDisabled')
            ->with(true);
        $this->generalInitMock($isActiveMock);

        $this->assertSame($this->observer, $this->observer->execute($this->eventObserverMock));
    }

    /**
     * @return void
     */
    public function testCurrentUserCanPublishRevision()
    {
        /** @var Select|MockObject $isActiveMock */
        $isActiveMock = $this->getMock('Magento\Framework\Data\Form\Element\Select', ['setDisabled'], [], '', false);
        $isActiveMock->expects($this->never())
            ->method('setDisabled');
        $this->configMock->expects($this->once())
            ->method('canCurrentUserPublishRevision')
            ->willReturn(true);
        $this->generalInitMock($isActiveMock);

        $this->assertSame($this->observer, $this->observer->execute($this->eventObserverMock));
    }

    /**
     * @return void
     */
    public function testSwitcherPublishIsAbsent()
    {
        $this->configMock->expects($this->never())
            ->method('canCurrentUserPublishRevision');
        $this->generalInitMock();

        $this->assertSame($this->observer, $this->observer->execute($this->eventObserverMock));
    }

    /**
     * @return void
     */
    public function testAddFieldWithRevisionStatus()
    {
        $this->generalInitMock();
        $this->pageMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->pageId);
        $this->pageMock->expects($this->once())
            ->method('getUnderVersionControl')
            ->willReturn(true);
        $this->pageMock->expects($this->once())
            ->method('setPublishedRevisionStatus')
            ->with(__('The published revision is unavailable.'));

        $this->assertSame($this->observer, $this->observer->execute($this->eventObserverMock));
    }

    /**
     * @return void
     */
    public function testGetRevisionWithUserCanSaveRevision()
    {
        $this->generalMocksForGetRevisionTest();

        $this->authorizationMock->expects($this->once())
            ->method('isAllowed')
            ->with('Magento_VersionsCms::save_revision')
            ->willReturn(true);

        $this->assertSame($this->observer, $this->observer->execute($this->eventObserverMock));
    }

    /**
     * @return void
     */
    public function testGetRevisionWithUserCanNotSaveRevision()
    {
        $this->generalMocksForGetRevisionTest();

        $this->authorizationMock->expects($this->once())
            ->method('isAllowed')
            ->with('Magento_VersionsCms::save_revision')
            ->willReturn(false);
        $this->fieldsetMock->expects($this->once())
            ->method('getElements')
            ->willReturn([
                $this->getAbstractElement(),
                $this->getAbstractElement()
            ]);

        $this->assertSame($this->observer, $this->observer->execute($this->eventObserverMock));
    }

    /**
     * @return AbstractElement|MockObject
     */
    protected function getAbstractElement()
    {
        /** @var AbstractElement|MockObject $elementMock */
        $elementMock = $this->getMockBuilder('Magento\Framework\Data\Form\Element\AbstractElement')
            ->disableOriginalConstructor()
            ->setMethods(['setDisabled'])
            ->getMock();
        $elementMock->expects($this->once())
            ->method('setDisabled')
            ->with(true);

        return $elementMock;
    }

    /**
     * @return void
     */
    protected function generalMocksForGetRevisionTest()
    {
        $userId = 3;
        $accessLevel = 1;
        $revisionNumber = 4;
        $versionLabel = 'Revision Label';
        $this->generalInitMock();
        $this->pageMock->expects($this->any())
            ->method('getPublishedRevisionId')
            ->willReturn($this->revisionId);
        $this->pageMock->expects($this->once())
            ->method('getUnderVersionControl')
            ->willReturn(true);
        $this->pageMock->expects($this->once())
            ->method('setPublishedRevisionLink')
            ->with(__('%1; rev #%2', $versionLabel, $revisionNumber));
        $this->pageMock->expects($this->any())
            ->method('getId')
            ->willReturn($this->pageId);

        /** @var \Magento\User\Model\User|MockObject $userMock */
        $userMock = $this->getMock('Magento\User\Model\User', ['getId'], [], '', false);
        $userMock->expects($this->once())
            ->method('getId')
            ->willReturn($userId);
        $this->backendAuthSessionMock->expects($this->once())
            ->method('getUser')
            ->willReturn($userMock);

        $this->configMock->expects($this->once())
            ->method('getAllowedAccessLevel')
            ->willReturn($accessLevel);

        /** @var \Magento\VersionsCms\Model\Page\Revision|MockObject $revisionMock */
        $revisionMock = $this->getMockBuilder('\Magento\VersionsCms\Model\Page\Revision')
            ->disableOriginalConstructor()
            ->setMethods(['getLabel', 'getId', 'loadWithRestrictions', 'getRevisionNumber'])
            ->getMock();
        $revisionMock->expects($this->once())
            ->method('loadWithRestrictions')
            ->with($accessLevel, $userId, $this->revisionId)
            ->willReturnSelf();
        $revisionMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->revisionId);
        $revisionMock->expects($this->once())
            ->method('getRevisionNumber')
            ->willReturn($revisionNumber);
        $revisionMock->expects($this->once())
            ->method('getLabel')
            ->willReturn($versionLabel);
        $this->revisionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($revisionMock);

        $this->backendUrlMock->expects($this->once())
            ->method('getUrl')
            ->with(
                'adminhtml/cms_page_revision/edit',
                ['page_id' => $this->pageId, 'revision_id' => $this->revisionId]
            )
            ->willReturn($this->url);
    }

    /**
     * @param Select|null$isActiveMock
     * @return void
     */
    protected function generalInitMock($isActiveMock = null)
    {
        $optionalArray = ['Yes', 'No'];
        $this->sourceYesnoMock->expects($this->once())
            ->method('toOptionArray')
            ->willReturn($optionalArray);

        $this->fieldsetMock->expects($this->atLeastOnce())
            ->method('addField')
            ->willReturnMap([
                [
                    'under_version_control',
                    'select',
                    [
                        'label' => __('Under Version Control'),
                        'title' => __('Under Version Control'),
                        'name' => 'under_version_control',
                        'values' => $optionalArray
                    ],
                    $this->fieldsetMock
                ],
                ['published_revision_status', 'label', ['bold' => true], $this->fieldsetMock],
                [
                    'published_revision_link',
                    'link',
                    ['label' => __('Currently Published Revision'), 'href' => $this->url],
                    $this->fieldsetMock
                ],
            ]);

        /** @var Form|MockObject $formMock */
        $formMock = $this->getMock('Magento\Framework\Data\Form', [], [], '', false);
        $formMock->expects($this->any())
            ->method('getElement')
            ->willReturnMap([
                ['base_fieldset', $this->fieldsetMock],
                ['is_active', $isActiveMock]
            ]);

        /** @var Event|MockObject $eventMock */
        $eventMock = $this->getMock('Magento\Framework\Event', ['getForm'], [], '', false);
        $eventMock->expects($this->once())
            ->method('getForm')
            ->willReturn($formMock);
        $this->eventObserverMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);
    }
}
