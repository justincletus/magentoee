<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\App\Action\Plugin;

class Design
{
    /**
     * @var \Magento\Framework\View\DesignLoader
     */
    protected $_designLoader;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @param \Magento\Framework\View\DesignLoader $designLoader
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Magento\Framework\View\DesignLoader $designLoader,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\State $appState
    ) {
        $this->_request = $request;
        $this->_appState = $appState;
        $this->_designLoader = $designLoader;
    }

    /**
     * Initialize design
     *
     * @param \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\RevisionInterface $subject
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDispatch(
        \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\RevisionInterface $subject,
        \Magento\Framework\App\RequestInterface $request
    ) {
        if ($this->_request->getActionName() == 'drop') {
            $this->_appState->emulateAreaCode('frontend', [$this, 'emulateDesignCallback']);
        } else {
            $this->_designLoader->load();
        }
    }

    /**
     * Callback for init design from outside (need to substitute area code)
     *
     * @return void
     */
    public function emulateDesignCallback()
    {
        $this->_designLoader->load();
    }
}
