<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\App;

class Preview extends \Magento\Backend\App\BackendApp
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @param string $cookiePath
     * @param string $startupPage
     * @param string $aclResourceName
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        $cookiePath,
        $startupPage,
        $aclResourceName,
        \Magento\Framework\App\Request\Http $request
    ) {
        parent::__construct($cookiePath, $startupPage, $aclResourceName);
        $this->request = $request;
    }

    /**
     * Cookie path for the application to set cookie to
     *
     * @return string
     */
    public function getCookiePath()
    {
        $basePath = $this->request->getUri()->getPath();
        $path = parent::getCookiePath();

        return $basePath . $path;
    }
}
