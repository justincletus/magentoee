<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Controller\Adminhtml\Position;

class Save extends \Magento\VisualMerchandiser\Controller\Adminhtml\Position
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $cacheKey = $this->getRequest()->getParam(
            \Magento\VisualMerchandiser\Model\Position\Cache::POSITION_CACHE_KEY
        );

        $positions = $this->getRequest()->getParam('positions', false) ?
            $this->getRequest()->getParam('positions', false) : [];

        $this->cache->saveData(
            $cacheKey,
            $positions,
            $this->getRequest()->getParam('sort_order', null)
        );

        $resultJson->setData([]);
        return $resultJson;
    }
}
