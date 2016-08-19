<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Model;


/**
 * Versions cms Logging handler
 */
class Logging
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(\Magento\Framework\App\RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Handler for cms hierarchy view
     *
     * @param array $config
     * @param \Magento\Logging\Model\Event $eventModel
     * @return \Magento\Logging\Model\Event|false
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function postDispatchCmsHierachyView($config, $eventModel)
    {
        return $eventModel->setInfo(__('Tree Viewed'));
    }

    /**
     * Handler for cms revision preview
     *
     * @param array $config
     * @param \Magento\Logging\Model\Event $eventModel
     * @return \Magento\Logging\Model\Event|false
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function postDispatchCmsRevisionPreview($config, $eventModel)
    {
        return $eventModel->setInfo($this->request->getParam('revision_id'));
    }

    /**
     * Handler for cms revision publish
     *
     * @param array $config
     * @param \Magento\Logging\Model\Event $eventModel
     * @return \Magento\Logging\Model\Event|false
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function postDispatchCmsRevisionPublish($config, $eventModel)
    {
        return $eventModel->setInfo($this->request->getParam('revision_id'));
    }
}
