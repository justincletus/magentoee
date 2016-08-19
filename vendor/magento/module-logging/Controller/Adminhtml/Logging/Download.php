<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Logging\Controller\Adminhtml\Logging;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends \Magento\Logging\Controller\Adminhtml\Logging
{
    /**
     * Download archive file
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $archive = $this->_archiveFactory->create()->loadByBaseName($this->getRequest()->getParam('basename'));
        if ($archive->getFilename()) {
            return $this->_fileFactory->create(
                $archive->getBaseName(),
                $archive->getContents(),
                DirectoryList::VAR_DIR,
                $archive->getMimeType()
            );
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Logging::backups');
    }
}
