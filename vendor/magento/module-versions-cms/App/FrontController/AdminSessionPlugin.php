<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\App\FrontController;

use Magento\Framework\App;

/**
 * Plugin for Magento\Framework\App\FrontControllerInterface
 */
class AdminSessionPlugin
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var App\RequestInterface
     */
    protected $request;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        App\RequestInterface $request
    ) {
        $this->objectManager = $objectManager;
        $this->request = $request;
    }

    /**
     * Activate admin session on frontend
     *
     * @param App\FrontController $subject
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDispatch(App\FrontController $subject)
    {
        $app = $this->request->getQuery('app');
        $path = $this->request->getRequestUri();
        if ($app == 'cms_preview' && strpos($path, \Magento\VersionsCms\Model\Page\Revision::PREVIEW_URI) !== false) {

            $config = $this->objectManager->create(
                'Magento\Backend\Model\Session\AdminConfig',
                ['sessionName' => 'admin']
            );

            $this->objectManager->create('Magento\Backend\Model\Auth\Session', ['sessionConfig' => $config]);
        }
    }
}
