<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision;

use Magento\VersionsCms\Controller\Adminhtml\Cms\Page\RevisionInterface;

class Versions extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Versions implements RevisionInterface
{
    /**
     * {@inheritdoc}
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page');
    }
}
