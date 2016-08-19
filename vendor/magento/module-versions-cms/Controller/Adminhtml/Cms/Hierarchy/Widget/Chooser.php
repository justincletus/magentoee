<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Hierarchy\Widget;

class Chooser extends \Magento\Backend\App\Action
{
    /**
     * Tree block instance
     *
     * @return \Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Widget\Chooser
     */
    protected function _getTreeBlock()
    {
        return $this->_view->getLayout()->createBlock(
            'Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Widget\Chooser',
            '',
            ['data' => ['id' => $this->getRequest()->getParam('uniq_id')]]
        );
    }

    /**
     * Chooser Source action
     *
     * @return void
     */
    public function execute()
    {
        $html = $this->_getTreeBlock()->setScope($this->getRequest()->getParam('scope'))
            ->setScopeId((int)$this->getRequest()->getParam('scope_id'))
            ->toHtml();
        $this->getResponse()->setBody($html);
    }
}
