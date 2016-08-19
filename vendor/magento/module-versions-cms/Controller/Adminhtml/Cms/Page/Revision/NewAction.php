<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision;

use Magento\VersionsCms\Controller\Adminhtml\Cms\Page\RevisionInterface;

class NewAction extends Edit implements RevisionInterface
{
    /**
     * Forward to edit
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
